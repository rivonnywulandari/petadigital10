<?= $this->extend('maps/main'); ?>

<?= $this->section('content') ?>

    <section class="section">
        <div class="row">
            <script>currentUrl = '<?= current_url(); ?>';</script>

            <!-- Object Detail Information -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Campus Information</h4>
                        
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
                                        <td class="fw-bold">District</td>
                                        <td><?= esc($data['district']); ?></td>
                                    </tr>                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Object Media -->

            </div>
        </div>
    </section>

    
<?= $this->endSection() ?>