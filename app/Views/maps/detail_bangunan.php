<?= $this->extend('maps/main'); ?>

<?= $this->section('content') ?>

    <section class="section">
        <div class="row">
            <script>currentUrl = '<?= current_url(); ?>';</script>

            <!-- Object Detail Information -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Bangunan Information</h4>
                        
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                    <tr>
                                        <td class="fw-bold">Name</td>
                                        <td><?= esc($data['name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Address</td>
                                        <td><?= esc($data['address']); ?></td>
                                    </tr>                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="fw-bold">Description</p>
                                <p><?= esc($data['description']); ?></p>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Object Media -->
            <?= $this->include('web/layouts/gallery_video'); ?>

            </div>
        </div>
    </section>

    
    <script>
        const myModal = document.getElementById('videoModal');
        const videoSrc = document.getElementById('video-play').getAttribute('data-src');

        myModal.addEventListener('shown.bs.modal', () => {
            console.log(videoSrc);
            document.getElementById('video').setAttribute('src', videoSrc);
        });
        myModal.addEventListener('hide.bs.modal', () => {
            document.getElementById('video').setAttribute('src', '');
        });
    </script>
<?= $this->endSection() ?>