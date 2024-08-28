let baseUrl = "";
let currentUrl = "";
let currentLat = 0,
  currentLng = 0;
let userLat = 0,
  userLng = 0;
let web, map;
let infoWindow = new google.maps.InfoWindow();
let userInfoWindow = new google.maps.InfoWindow();
let directionsService, directionsRenderer;
let userMarker = new google.maps.Marker();
let destinationMarker = new google.maps.Marker();
let routeArray = [],
  circleArray = [],
  markerArray = {};
let bounds = new google.maps.LatLngBounds();
let selectedShape,
  drawingManager = new google.maps.drawing.DrawingManager();
let customStyled = [
  {
    elementType: "labels",
    stylers: [
      {
        visibility: "off",
      },
    ],
  },
  {
    featureType: "administrative.land_parcel",
    stylers: [
      {
        visibility: "off",
      },
    ],
  },
  {
    featureType: "administrative.neighborhood",
    stylers: [
      {
        visibility: "off",
      },
    ],
  },
  {
    featureType: "road",
    elementType: "labels",
    stylers: [
      {
        visibility: "on",
      },
    ],
  },
];

function setBaseUrl(url) {
  baseUrl = url;
}

// Initialize and add the map
function initMap(lat = -0.91471072, lng = 100.45892816) {
  directionsService = new google.maps.DirectionsService();
  const center = new google.maps.LatLng(lat, lng);

  map = new google.maps.Map(document.getElementById("googlemaps"), {
    zoom: 15,
    center: center,
    mapTypeId: "roadmap",
  });

  var rendererOptions = {
    map: map,
  };
  map.set("styles", customStyled);
  directionsRenderer = new google.maps.DirectionsRenderer(rendererOptions);
  digitCampus();
}

// Display campus digitizing
function digitCampus() {
  const campus = new google.maps.Data();
  $.ajax({
    url: baseUrl + "/api/campus",
    type: "POST",
    data: {
      campus: "1",
    },
    dataType: "json",
    success: function (response) {
      const data = response.data;
      campus.addGeoJson(data);
      campus.setStyle({
        fillColor: "#00b300",
        strokeWeight: 0.5,
        strokeColor: "#005000",
        fillOpacity: 0.1,
        clickable: false,
      });
      campus.setMap(map);
    },
  });
}

// Remove user location
function clearUser() {
  userLat = 0;
  userLng = 0;
  userMarker.setMap(null);
}

// Set current location based on user location
function setUserLoc(lat, lng) {
  userLat = lat;
  userLng = lng;
  currentLat = userLat;
  currentLng = userLng;
}

// Remove any route shown
function clearRoute() {
  for (i in routeArray) {
    routeArray[i].setMap(null);
  }
  routeArray = [];
  $("#direction-row").hide();
}

// Remove any radius shown
function clearRadius() {
  for (i in circleArray) {
    circleArray[i].setMap(null);
  }
  circleArray = [];
}

// Remove any marker shown
function clearMarker() {
  for (i in markerArray) {
    markerArray[i].setMap(null);
  }
  markerArray = {};
}

// Get user's current position
function currentPosition() {
  clearRadius();
  clearRoute();

  google.maps.event.clearListeners(map, "click");
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        infoWindow.close();
        clearUser();
        markerOption = {
          position: pos,
          animation: google.maps.Animation.DROP,
          map: map,
        };
        userMarker.setOptions(markerOption);
        userInfoWindow.setContent(
          "<p class='text-center'><span class='fw-bold'>You are here.</span> <br> lat: " +
            pos.lat +
            "<br>long: " +
            pos.lng +
            "</p>"
        );
        userInfoWindow.open(map, userMarker);
        map.setCenter(pos);
        setUserLoc(pos.lat, pos.lng);

        userMarker.addListener("click", () => {
          userInfoWindow.open(map, userMarker);
        });
      },
      () => {
        handleLocationError(true, userInfoWindow, map.getCenter());
      }
    );
  } else {
    // Browser doesn't support Geolocation
    handleLocationError(false, userInfoWindow, map.getCenter());
  }
}

// Error handler for geolocation
function handleLocationError(browserHasGeolocation, infoWindow, pos) {
  infoWindow.setPosition(pos);
  infoWindow.setContent(
    browserHasGeolocation
      ? "Error: The Geolocation service failed."
      : "Error: Your browser doesn't support geolocation."
  );
  infoWindow.open(map);
}

// User set position on map
function manualPosition() {
  clearRadius();
  clearRoute();

  if (userLat == 0 && userLng == 0) {
    Swal.fire("Click on Map");
  }
  map.addListener("click", (mapsMouseEvent) => {
    infoWindow.close();
    pos = mapsMouseEvent.latLng;

    clearUser();
    markerOption = {
      position: pos,
      animation: google.maps.Animation.DROP,
      map: map,
    };
    userMarker.setOptions(markerOption);
    userInfoWindow.setContent(
      "<p class='text-center'><span class='fw-bold'>You are here.</span> <br> lat: " +
        pos.lat().toFixed(8) +
        "<br>long: " +
        pos.lng().toFixed(8) +
        "</p>"
    );
    userInfoWindow.open(map, userMarker);

    userMarker.addListener("click", () => {
      userInfoWindow.open(map, userMarker);
    });

    setUserLoc(pos.lat().toFixed(8), pos.lng().toFixed(8));
  });
}

// Render route on selected object
function routeTo(lat, lng, routeFromUser = true) {
  clearRadius();
  clearRoute();
  google.maps.event.clearListeners(map, "click");

  let start, end;
  if (routeFromUser) {
    if (userLat == 0 && userLng == 0) {
      return Swal.fire("Determine your position first!");
    }
    setUserLoc(userLat, userLng);
  }
  start = new google.maps.LatLng(currentLat, currentLng);
  end = new google.maps.LatLng(lat, lng);
  let request = {
    origin: start,
    destination: end,
    travelMode: "DRIVING",
  };
  directionsService.route(request, function (result, status) {
    if (status == "OK") {
      directionsRenderer.setDirections(result);
      showSteps(result);
      directionsRenderer.setMap(map);
      routeArray.push(directionsRenderer);
    }
  });
  boundToRoute(start, end);
}

// Initialize drawing manager on maps
function objectMarker2() {
  const drawingManagerOpts = {
    drawingMode: google.maps.drawing.OverlayType.POLYGON,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: [google.maps.drawing.OverlayType.POLYGON],
    },
    polygonOptions: {
      fillColor: "blue",
      strokeColor: "blue",
      editable: true,
    },
    map: map,
  };
  drawingManager.setOptions(drawingManagerOpts);
  let newShape;

  drawingManager.setOptions({
    drawingControl: false,
    drawingMode: null,
  });

  newShape = drawGeom();
  newShape.type = "polygon";
  setSelection(newShape);

  const paths = newShape.getPath().getArray();
  let bounds = new google.maps.LatLngBounds();
  for (let i = 0; i < paths.length; i++) {
    bounds.extend(paths[i]);
  }
  let pos = bounds.getCenter();
  map.panTo(pos);

  clearMarker();
  let marker = new google.maps.Marker();
  markerOption = {
    position: pos,
    animation: google.maps.Animation.DROP,
    map: map,
  };
  marker.setOptions(markerOption);
  markerArray["newRG"] = marker;

  google.maps.event.addListener(newShape, "click", function () {
    setSelection(newShape);
  });
  google.maps.event.addListener(newShape.getPath(), "insert_at", () => {
    saveSelection(newShape);
  });
  google.maps.event.addListener(newShape.getPath(), "remove_at", () => {
    saveSelection(newShape);
  });
  google.maps.event.addListener(newShape.getPath(), "set_at", () => {
    saveSelection(newShape);
  });

  google.maps.event.addListener(map, "click", clearSelection);
  google.maps.event.addDomListener(
    document.getElementById("clear-drawing"),
    "click",
    deleteSelectedShape
  );
}

// Display marker for loaded object
function objectMarker(id, category_id, lat, lng, anim = true) {
  google.maps.event.clearListeners(map, "click");
  let pos = new google.maps.LatLng(lat, lng);
  let marker = new google.maps.Marker();

  // let icon;
  // if (id.substring(0, 1) === "B") {
  //   icon = baseUrl + "/media/icon/locationred.png";
  // }

  let icon;
  if (id.substring(0, 1) === "B" && category_id === "01") {
    icon = baseUrl + "/media/icon/marker_red.png";
  } else if (id.substring(0, 1) === "B" && category_id === "02") {
    icon = baseUrl + "/media/icon/marker_blue.png";
  } else if (id.substring(0, 1) === "B" && category_id === "03") {
    icon = baseUrl + "/media/icon/marker_purple.png";
  } else if (id.substring(0, 1) === "B" && category_id === "04") {
    icon = baseUrl + "/media/icon/marker_brown.png";
  } else if (id.substring(0, 1) === "B" && category_id === "05") {
    icon = baseUrl + "/media/icon/marker_yellow.png";
  }

  markerOption = {
    position: pos,
    icon: icon,
    animation: google.maps.Animation.DROP,
    map: map,
  };
  marker.setOptions(markerOption);
  if (!anim) {
    marker.setAnimation(null);
  }
  marker.addListener("click", () => {
    infoWindow.close();
    objectInfoWindow(id);
    infoWindow.open(map, marker);
  });
  markerArray[id] = marker;

  // Menambahkan polygon
  const polygonOptions = {
    paths: [pos],
    strokeColor: "blue",
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "blue",
    fillOpacity: 0.35,
    editable: false, // Tidak dapat diedit
    map: map,
  };

  const polygon = new google.maps.Polygon(polygonOptions);
}

// Display info window for loaded object
function objectInfoWindow(id) {
  let content = "";
  let contentButton = "";

  if (id.substring(0, 1) === "B") {
    $.ajax({
      url: baseUrl + "/api/bangunan/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let rgid = data.id;
        let name = data.name;
        let address = data.address;
        let lat = data.lat;
        let lng = data.lng;

        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p> <br>" +
          '<p><i class="fa-solid fa-money-bill me-2"></i> ' +
          address +
          "</p>" +
          "</div>";

        contentButton =
          '<br><div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href=' +
          baseUrl +
          "/web/bangunan/" +
          rgid +
          '><i class="fa-solid fa-info"></i></a>' +
          "</div>";

        if (currentUrl.includes(id)) {
          infoWindow.setContent(content);
          infoWindow.open(map, markerArray[rgid]);
        } else {
          infoWindow.setContent(content + contentButton);
        }
      },
    });
  }
}

// Render map to contains all object marker
function boundToObject(firstTime = true) {
  if (Object.keys(markerArray).length > 0) {
    bounds = new google.maps.LatLngBounds();
    for (i in markerArray) {
      bounds.extend(markerArray[i].getPosition());
    }
    if (firstTime) {
      map.fitBounds(bounds, 80);
    } else {
      map.panTo(bounds.getCenter());
    }
  } else {
    let pos = new google.maps.LatLng(-0.91471072, 100.45892816);
    map.panTo(pos);
  }
}

// Render map to contains route and its markers
function boundToRoute(start, end) {
  bounds = new google.maps.LatLngBounds();
  bounds.extend(start);
  bounds.extend(end);
  map.panToBounds(bounds, 100);
}

// Add user position to map bound
function boundToRadius(lat, lng, rad) {
  let userBound = new google.maps.LatLng(lat, lng);
  const radiusCircle = new google.maps.Circle({
    center: userBound,
    radius: Number(rad),
  });
  map.fitBounds(radiusCircle.getBounds());
}

// Draw radius circle
function drawRadius(position, radius) {
  const radiusCircle = new google.maps.Circle({
    center: position,
    radius: radius,
    map: map,
    strokeColor: "#FF0000",
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "#FF0000",
    fillOpacity: 0.35,
  });
  circleArray.push(radiusCircle);
  boundToRadius(currentLat, currentLng, radius);
}

// pan to selected object
function focusObject(id) {
  google.maps.event.trigger(markerArray[id], "click");
  map.panTo(markerArray[id].getPosition());
}

// display objects by feature used
function displayFoundObject(response) {
  $("#table-data").empty();
  let data = response.data;
  let counter = 1;
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  for (i in data) {
    let item = data[i];
    let row;
    if (item.hasOwnProperty("date_next")) {
      let date_next = new Date(item.date_next);
      let next =
        date_next.getDate() +
        " " +
        months[date_next.getMonth()] +
        " " +
        date_next.getFullYear();
      row =
        "<tr>" +
        "<td>" +
        counter +
        "</td>" +
        '<td class="fw-bold">' +
        item.name +
        '<br><span class="text-muted">' +
        next +
        "</span></td>" +
        "<td>" +
        '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Info" class="btn icon btn-primary mx-1" onclick="focusObject(`' +
        item.id +
        '`);">' +
        '<span class="material-symbols-outlined">info</span>' +
        "</a>" +
        "</td>" +
        "</tr>";
    } else {
      row =
        "<tr>" +
        "<td>" +
        counter +
        "</td>" +
        '<td class="fw-bold">' +
        item.name +
        "</td>" +
        "<td>" +
        '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Info" class="btn icon btn-primary mx-1" onclick="focusObject(`' +
        item.id +
        '`);">' +
        '<span class="material-symbols-outlined">info</span>' +
        "</a>" +
        "</td>" +
        "</tr>";
    }
    $("#table-data").append(row);
    objectMarker(item.id, item.category_id, item.lat, item.lng);
    counter++;
  }
}

// display steps of direction to selected route
function showSteps(directionResult) {
  $("#direction-row").show();
  $("#table-direction").empty();
  let myRoute = directionResult.routes[0].legs[0];
  for (let i = 0; i < myRoute.steps.length; i++) {
    let distance = myRoute.steps[i].distance.value;
    let instruction = myRoute.steps[i].instructions;
    let row =
      "<tr>" +
      "<td>" +
      distance.toLocaleString("id-ID") +
      "</td>" +
      "<td>" +
      instruction +
      "</td>" +
      "</tr>";
    $("#table-direction").append(row);
  }
}

// Find object by name
function findByName(category) {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  let name;
  if (category === "BG") {
    name = document.getElementById("nameBG").value;
    $.ajax({
      url: baseUrl + "/api/bangunan/findByName",
      type: "POST",
      data: {
        name: name,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  }
}

// Find object by Category
function findByCategory(object) {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  if (object === "BG") {
    let category = document.getElementById("categoryBGSelect").value;
    $.ajax({
      url: baseUrl + "/api/bangunan/findByCategory",
      type: "POST",
      data: {
        category: category,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  }
}

// Get list of Bangunan type or category
function getCategory() {
  let category;
  $("#categoryBGSelect").empty();
  $.ajax({
    url: baseUrl + "/api/bangunan/category",
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        category =
          '<option value="' + item.id + '">' + item.category + "</option>";
        $("#categoryBGSelect").append(category);
      }
    },
  });
}

// Create compass
function setCompass() {
  const compass = document.createElement("div");
  compass.setAttribute("id", "compass");
  const compassDiv = document.createElement("div");
  compass.appendChild(compassDiv);
  const compassImg = document.createElement("img");
  compassImg.src = baseUrl + "/media/icon/compass.png";
  compassDiv.appendChild(compassImg);

  map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(compass);
}

// Create legend
function getLegend() {
  const icons = {
    fu: {
      name: "Gedung Fasilitas Umum",
      icon: baseUrl + "/media/icon/marker_red.png",
    },
    gf: {
      name: "Gedung Fakultas",
      icon: baseUrl + "/media/icon/marker_blue.png",
    },
    gk: {
      name: "Gedung Kuliah",
      icon: baseUrl + "/media/icon/marker_purple.png",
    },
    ga: {
      name: "Gedung Asrama",
      icon: baseUrl + "/media/icon/marker_brown.png",
    },
    lo: {
      name: "Lapangan Olahraga",
      icon: baseUrl + "/media/icon/marker_yellow.png",
    },
  };

  const title = '<p class="fw-bold fs-6">Legend</p>';
  $("#legend").append(title);

  for (key in icons) {
    const type = icons[key];
    const name = type.name;
    const icon = type.icon;
    const div = '<div><img src="' + icon + '"> ' + name + "</div>";

    $("#legend").append(div);
  }
  map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
}

// toggle legend element
function viewLegend() {
  if ($("#legend").is(":hidden")) {
    $("#legend").show();
  } else {
    $("#legend").hide();
  }
}

// Check if Category and Object is chose correctly
function checkForm(event) {
  const category = document.getElementById("category").value;
  const object = document.getElementById("object").value;
  if (category === "None" || object === "None") {
    event.preventDefault();
    Swal.fire("Please select the correct Category and Object");
  }
}

// Update preview of uploaded photo profile
function showPreview(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $("#avatar-preview").attr("src", e.target.result).width(300).height(300);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Get list of Recommendation
function getRecommendation(id, recom) {
  let recommendation;
  $("#recommendationSelect" + id).empty();
  $.ajax({
    url: baseUrl + "/api/recommendationList",
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        if (item.id == recom) {
          recommendation =
            '<option value="' +
            item.id +
            '" selected>' +
            item.name +
            "</option>";
        } else {
          recommendation =
            '<option value="' + item.id + '">' + item.name + "</option>";
        }
        $("#recommendationSelect" + id).append(recommendation);
      }
    },
  });
}

// Update option onclick function for updating Recommendation
function changeRecom(status = null) {
  if (status === "edit") {
    $("#recomBtnEdit").hide();
    $("#recomBtnExit").show();
    console.log("entering edit mode");
    $(".recomSelect").on("change", updateRecom);
  } else {
    $("#recomBtnEdit").show();
    $("#recomBtnExit").hide();
    console.log("exiting edit mode");
    $(".recomSelect").off("change", updateRecom);
  }
}

// Update recommendation based on input User
function updateRecom() {
  let recom = $(this).find("option:selected").val();
  let id = $(this).attr("id");
  $.ajax({
    url: baseUrl + "/api/recommendation",
    type: "POST",
    data: {
      id: id,
      recom: recom,
    },
    dataType: "json",
    success: function (response) {
      if (response.status === 201) {
        console.log("Success update recommendation @" + id + ":" + recom);
        Swal.fire("Success updating Bangunan ID @" + id);
      }
    },
  });
}

// Set map to coordinate put by user
function findCoords(object) {
  clearMarker();
  google.maps.event.clearListeners(map, "click");

  const lat = Number(document.getElementById("latitude").value);
  const lng = Number(document.getElementById("longitude").value);

  if (lat === 0 || lng === 0 || isNaN(lat) || isNaN(lng)) {
    return Swal.fire("Please input Lat and Long");
  }

  let pos = new google.maps.LatLng(lat, lng);
  map.panTo(pos);
}

// Unselect shape on drawing map
function clearSelection() {
  if (selectedShape) {
    selectedShape.setEditable(false);
    selectedShape = null;
  }
}

// Make selected shape editable on maps
function setSelection(shape) {
  clearSelection();
  selectedShape = shape;
  shape.setEditable(true);
}

// Remove selected shape on maps
function deleteSelectedShape() {
  if (selectedShape) {
    document.getElementById("latitude").value = "";
    document.getElementById("longitude").value = "";
    document.getElementById("geo-json").value = "";
    document.getElementById("lat").value = "";
    document.getElementById("lng").value = "";
    clearMarker();
    selectedShape.setMap(null);
    // To show:
    drawingManager.setOptions({
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      drawingControl: true,
    });
  }
}

// Initialize drawing manager on maps
function initDrawingManager(edit = false) {
  const drawingManagerOpts = {
    drawingMode: google.maps.drawing.OverlayType.POLYGON,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: [google.maps.drawing.OverlayType.POLYGON],
    },
    polygonOptions: {
      fillColor: "blue",
      strokeColor: "blue",
      editable: true,
    },
    map: map,
  };
  drawingManager.setOptions(drawingManagerOpts);
  let newShape;

  if (!edit) {
    google.maps.event.addListener(
      drawingManager,
      "overlaycomplete",
      function (event) {
        drawingManager.setOptions({
          drawingControl: false,
          drawingMode: null,
        });
        newShape = event.overlay;
        newShape.type = event.type;
        setSelection(newShape);
        saveSelection(newShape);

        google.maps.event.addListener(newShape, "click", function () {
          setSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "insert_at", () => {
          saveSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "remove_at", () => {
          saveSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "set_at", () => {
          saveSelection(newShape);
        });
      }
    );
  } else {
    drawingManager.setOptions({
      drawingControl: false,
      drawingMode: null,
    });

    newShape = drawGeom();
    newShape.type = "polygon";
    setSelection(newShape);

    const paths = newShape.getPath().getArray();
    let bounds = new google.maps.LatLngBounds();
    for (let i = 0; i < paths.length; i++) {
      bounds.extend(paths[i]);
    }
    let pos = bounds.getCenter();
    map.panTo(pos);

    clearMarker();
    let marker = new google.maps.Marker();
    markerOption = {
      position: pos,
      animation: google.maps.Animation.DROP,
      map: map,
    };
    marker.setOptions(markerOption);
    markerArray["newRG"] = marker;

    google.maps.event.addListener(newShape, "click", function () {
      setSelection(newShape);
    });
    google.maps.event.addListener(newShape.getPath(), "insert_at", () => {
      saveSelection(newShape);
    });
    google.maps.event.addListener(newShape.getPath(), "remove_at", () => {
      saveSelection(newShape);
    });
    google.maps.event.addListener(newShape.getPath(), "set_at", () => {
      saveSelection(newShape);
    });
  }

  google.maps.event.addListener(map, "click", clearSelection);
  google.maps.event.addDomListener(
    document.getElementById("clear-drawing"),
    "click",
    deleteSelectedShape
  );
}

// Get geoJSON of selected shape on map
function saveSelection(shape) {
  const paths = shape.getPath().getArray();
  let bounds = new google.maps.LatLngBounds();
  for (let i = 0; i < paths.length; i++) {
    bounds.extend(paths[i]);
  }
  let pos = bounds.getCenter();
  map.panTo(pos);

  clearMarker();
  let marker = new google.maps.Marker();
  markerOption = {
    position: pos,
    animation: google.maps.Animation.DROP,
    map: map,
  };
  marker.setOptions(markerOption);
  markerArray["newRG"] = marker;

  document.getElementById("latitude").value = pos.lat().toFixed(8);
  document.getElementById("longitude").value = pos.lng().toFixed(8);
  document.getElementById("lat").value = pos.lat().toFixed(8);
  document.getElementById("lng").value = pos.lng().toFixed(8);

  const dataLayer = new google.maps.Data();
  dataLayer.add(
    new google.maps.Data.Feature({
      geometry: new google.maps.Data.Polygon([shape.getPath().getArray()]),
    })
  );
  dataLayer.toGeoJson(function (object) {
    document.getElementById("geo-json").value = JSON.stringify(
      object.features[0].geometry
    );
  });
}

// Get list of users
function getListUsers(owner) {
  let users;
  $("#ownerSelect").empty();
  $.ajax({
    url: baseUrl + "/api/owner",
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        if (item.id == owner) {
          users =
            '<option value="' +
            item.id +
            '" selected>' +
            item.first_name +
            " " +
            item.last_name +
            "</option>";
        } else {
          users =
            '<option value="' +
            item.id +
            '">' +
            item.first_name +
            " " +
            item.last_name +
            "</option>";
        }
        $("#ownerSelect").append(users);
      }
    },
  });
}

// Draw current GeoJSON on drawing manager
function drawGeom() {
  const geoJSON = $("#geo-json").val();
  if (geoJSON !== "") {
    const geoObj = JSON.parse(geoJSON);
    const coords = geoObj.coordinates[0];
    let polygonCoords = [];
    for (i in coords) {
      polygonCoords.push({ lat: coords[i][1], lng: coords[i][0] });
    }
    const polygon = new google.maps.Polygon({
      paths: polygonCoords,
      fillColor: "blue",
      strokeColor: "blue",
      editable: true,
    });
    polygon.setMap(map);
    return polygon;
  }
}

// Delete selected object
function deleteObject(id = null, name = null, user = false) {
  if (id === null) {
    return Swal.fire("ID cannot be null");
  }

  let content, apiUri;
  if (id.substring(0, 1) === "B") {
    content = "Bangunan";
    apiUri = "bangunan/";
  } else if (user === true) {
    content = "User";
    apiUri = "user/";
  } else {
    content = "Category";
    apiUri = "category/";
  }

  Swal.fire({
    title: "Delete " + content + "?",
    text: "You are about to remove " + name,
    icon: "warning",
    showCancelButton: true,
    denyButtonText: "Delete",
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#343a40",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: baseUrl + "/api/" + apiUri + id,
        type: "DELETE",
        dataType: "json",
        success: function (response) {
          if (response.status === 200) {
            Swal.fire(
              "Deleted!",
              "Successfully remove " + name,
              "success"
            ).then((result) => {
              if (result.isConfirmed) {
                document.location.reload();
              }
            });
          } else {
            Swal.fire("Failed", "Delete " + name + " failed!", "warning");
          }
        },
      });
    }
  });
}
