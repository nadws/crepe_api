<form action="<?= route('saveEditJurnal') ?>" method="POST">
    @csrf
    <div class="modal-content">
        <div class="modal-header bg-costume">
            <h4 class="modal-title">Edit Jurnal</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (empty($jurnal_penutup->id_buku)) : ?>
                <div class="row">

                    <div class="col-sm-3 col-md-3">
                        <div class="form-group">
                            <label for="list_kategori">Tanggal</label>
                            <input class="form-control" type="date" name="tgl" value="<?= $debit->tgl ?>" required>

                        </div>
                    </div>
                    <input type="hidden" name="id_jurnal_kredit" value="<?= $kredit->id_jurnal ?>">

                    <div class="mt-3 ml-1">
                        <p class="mt-4 ml-2 text-warning"><strong>Db</strong></p>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="form-group">
                            <label for="list_kategori">Akun </label>
                            <select name="id_akun_debit" id="id_akun_edit" class="form-control select" required="">
                                <?php foreach ($akun as $a) : ?>
                                    <?php if ($debit->id_akun == $a->id_akun) : ?>
                                        <option value="<?= $a->id_akun ?>" selected><?= $a->nm_akun ?></option>
                                    <?php else : ?>
                                        <option value="<?= $a->id_akun ?>"><?= $a->nm_akun ?></option>
                                    <?php endif ?>

                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2 col-md-2">
                        <div class="form-group">
                            <label for="list_kategori">Debit</label>
                            <input type="number" class="form-control total_edit" id="total" name="total" value="<?= $kredit->kredit ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2 col-md-2">
                        <div class="form-group">
                            <label for="list_kategori">Kredit</label>
                            <input type="number" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3">

                    </div>

                    <div class="mt-1">
                        <p class="mt-1 ml-3 text-warning"><strong>Cr</strong></p>
                    </div>

                    <div class="col-sm-4 col-md-4">
                        <div class="form-group">
                            <select name="id_akun_kredit" id="metode" class="form-control select2bs4" required>';
                                <?php foreach ($akun as $a) : ?>
                                    <?php if ($kredit->id_akun == $a->id_akun) : ?>
                                        <option value="<?= $a->id_akun ?>" selected><?= $a->nm_akun ?></option>
                                    <?php else : ?>
                                        <option value="<?= $a->id_akun ?>"><?= $a->nm_akun ?></option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2 col-md-2">
                        <div class="form-group">
                            <input type="number" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2 col-md-2">
                        <div class="form-group">
                            <input type="number" name="kredit" class="form-control total_edit" value="<?= $kredit->kredit ?>" readonly>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3">

                    </div>
                </div>

                <?php foreach ($kelompok_debit as $d) : ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="list_kategori">No id</label>
                                <input type="text" class="form-control input_detail input_monitoring" name="no_id[]" value="<?= $d->no_urutan ?>" required>
                                <input type="hidden" value="<?= $d->id_jurnal ?>" name="id_jurnal_debit[]">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="list_kategori">Tujuan</label>
                                <input type="text" class="form-control input_detail input_monitoring" name="ket[]" value="<?= $d->ket ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="list_kategori">Keterangan</label>
                                <input type="text" class="form-control input_detail input_monitoring" name="ket2[]" value="<?= $d->ket2 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="list_kategori">Post Center</label>
                                <select name="id_post_center[]" id="" class="select form-control id_post">
                                    <option value=" ">--Pilih post--</option>
                                    <?php foreach ($post_center as $p) : ?>
                                        <?php if ($d->id_post == $p->id_post) : ?>
                                            <option value="<?= $p->id_post ?>" selected><?= $p->nm_post ?></option>
                                        <?php else : ?>
                                            <option value="<?= $p->id_post ?>"><?= $p->nm_post ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="list_kategori">Qty</label>
                                <input type="text" class="form-control input_detail input_monitoring" name="qty[]" value="<?= $d->qty ?>" required>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="list_kategori">Satuan</label>
                                <select name="id_satuan[]" class="form-control select satuan input_detail input_biaya" required>
                                    <?php foreach ($satuan as $p) : ?>
                                        <?php if ($p->id == $d->id_satuan) : ?>
                                            <option value="<?= $p->id ?>" selected><?= $p->n ?></option>
                                        <?php else : ?>
                                            <option value="<?= $p->id ?>"><?= $p->n ?></option>
                                        <?php endif ?>

                                    <?php endforeach; ?>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="list_kategori">Total Rp</label>
                                <input type="text" class="form-control  input_detail input_biaya debit_edit total_rp1" name="debit[]" value="<?= $d->debit ?>" total_rp='1' required>
                            </div>
                        </div>

                    </div>
                <?php endforeach ?>
            <?php else : ?>
                <div class="row justify-content-center">
                    <h5>Edit data tidak bisa dilakukan karena jurnal sudah ditutup</h5>
                </div>
            <?php endif ?>

        </div>
        <div class="modal-footer">
            <?php if (empty($jurnal_penutup->id_buku)) : ?>
                <button type="submit" class="btn btn-info">Save/Edit</button>
            <?php else : ?>
            <?php endif ?>

            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</form>