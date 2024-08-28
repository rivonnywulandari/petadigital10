<?php
$uri = service('uri')->getSegments();
$uri1 = $uri[1] ?? 'index';
$uri2 = $uri[2] ?? '';
$uri3 = $uri[3] ?? '';
?>

<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <!-- Sidebar Header -->
        <?= $this->include('web/layouts/sidebar_header'); ?>

        <!-- Sidebar -->
        <div class="sidebar-menu">
            <div class="d-flex flex-column">
                <div class="d-flex justify-content-center avatar avatar-xl me-3" id="avatar-sidebar">
                    <!-- <img src="<?= base_url('media/photos/logounand.png'); ?>" alt="" srcset=""> -->
                </div>
                <?php if (logged_in()) : ?>
                    <div class="p-2 text-center">
                        <?php if (!empty(user()->first_name)) : ?>
                            Hello, <span class="fw-bold"><?= user()->first_name; ?><?= (!empty(user()->last_name)) ? ' ' . user()->last_name : ''; ?></span> <br> <span class="text-muted mb-0">@<?= user()->username; ?></span>
                        <?php else : ?>
                            Hello, <span class="fw-bold">@<?= user()->username; ?></span>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <div class="p-2 d-flex justify-content-center">Hello, Visitor</div>
                <?php endif; ?>
                <ul class="menu">


                    <!-- Recommendation bangunan kampus -->
                    <li class="sidebar-item <?= ($uri1 == 'index') ? 'active' : '' ?>">
                        <a href="/web" class="sidebar-link">
                            <i class="fa-solid fa-house"></i><span>Recommendation</span>
                        </a>
                    </li>


                    <!-- Bangunan -->
                    <li class="sidebar-item <?= ($uri1 == 'bangunan') ? 'active' : '' ?> has-sub">
                        <a href="" class="sidebar-link">
                            <i class="fa-solid fa-school"></i><span>Buildings</span>
                        </a>

                        <ul class="submenu <?= ($uri1 == 'bangunan') ? 'active' : '' ?>">
                            <!-- List Bangunan -->
                            <li class="submenu-item" id="bg-list">
                                <a href="<?= base_url('/web/bangunan'); ?>"><i class="fa-solid fa-list me-3"></i>List</a>
                            </li>

                            <li class="submenu-item has-sub" id="bg-search">
                                <a data-bs-toggle="collapse" href="#subsubmenu-bg" role="button" aria-expanded="false" aria-controls="subsubmenu-bg" class="collapse"><i class="fa-solid fa-magnifying-glass me-3"></i>Search</a>
                                <ul class="subsubmenu collapse" id="subsubmenu-bg">
                                    <!-- Bangunan by Name -->
                                    <li class="submenu-item submenu-marker" id="bg-by-name">
                                        <a data-bs-toggle="collapse" href="#searchNameBG" role="button" aria-expanded="false" aria-controls="searchNameBG"><i class="fa-solid fa-arrow-down-a-z me-3"></i>By Name</a>
                                        <div class="collapse mb-3" id="searchNameBG">
                                            <div class="d-grid gap-2">
                                                <input type="text" name="nameBG" id="nameBG" class="form-control" placeholder="Name" aria-label="Recipient's username" aria-describedby="button-addon2">
                                                <button class="btn btn-outline-primary" type="submit" id="button-addon2" onclick="findByName('BG')">
                                                    <span class="material-icons" style="font-size: 1.5rem; vertical-align: bottom">search</span>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- Bangunan by Category -->
                                    <li class="submenu-item submenu-marker" id="bg-by-category">
                                        <a data-bs-toggle="collapse" href="#searchCategoryBG" role="button" aria-expanded="false" aria-controls="searchCategoryBG"><i class="fa-solid fa-check-to-slot me-3"></i>By Category</a>
                                        <div class="collapse mb-3" id="searchCategoryBG">
                                            <div class="d-grid">
                                                <script>
                                                    getCategory();
                                                </script>
                                                <fieldset class="form-group">
                                                    <select class="form-select" id="categoryBGSelect">
                                                    </select>
                                                </fieldset>
                                                <button class="btn btn-outline-primary" type="submit" id="button-addon2" onclick="findByCategory('BG')">
                                                    <span class="material-icons" style="font-size: 1.5rem; vertical-align: bottom">search</span>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <?php if (in_groups(['admin'])) : ?>
                        <li class="sidebar-item">
                            <?php if (in_groups(['admin'])) : ?>
                                <a href="<?= base_url('dashboard/bangunan'); ?>" class="sidebar-link">
                               
                                    <?php endif; ?>
                                    <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                                    </a>
                        </li>
                    <?php endif; ?>

                    <li class="sidebar-item">
                        <div class="d-flex justify-content-around">
                            <a href="https://www.unand.ac.id" class="sidebar-link" target="_blank">
                                <i class="fa-solid fa-newspaper"></i><span>Website</span>
                            </a>
                            <a href="https://www.instagram.com/unandofficial" class="sidebar-link" target="_blank">
                                <i class="fa-brands fa-instagram"></i><span>Instagram</span>
                            </a>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</div>