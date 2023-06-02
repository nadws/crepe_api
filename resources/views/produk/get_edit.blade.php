<div class="row">
    <div class="col-sm-4 ol-md-6 col-xs-12 mb-2">
        <label for="">Masukkan Gambar</label>
        <input type="file" class="dropify" data-height="150" name="image" placeholder="Image">
    </div>
    <div class="col-lg-8">
        <div class="form-group row">

            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Nama Produk</dt>
                </label>
                <input type="text" name="nm_produk" class="form-control" placeholder="Nama Produk" required>
            </div>

            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Kategori</dt>
                </label>
                <select name="id_kategori" class="form-control select" required>
                    <option value="">-Pilih Kategori-</option>
                    @foreach ($kategori as $k)
                    <option value="{{$k->id_kategori}}">{{$k->nm_kategori}}</option>
                    @endforeach

                </select>
            </div>

            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Satuan</dt>
                </label>
                <select name="id_satuan" class="form-control select" id="" required>
                    <option value="">-Pilih Satuan-</option>
                    @foreach ($satuan as $s)
                    <option value="{{$s->id_satuan}}">{{$s->satuan}}</option>
                    @endforeach

                </select>
            </div>

            <!--<div class="col-lg-4 mb-2">-->
            <!--    <label for="">-->
            <!--        <dt>Stok</dt>-->
            <!--    </label>-->
            <!--    <input type="text" class="form-control" name="stok" placeholder="cth : 1" required>-->
            <!--</div>-->

            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Harga Modal</dt>
                </label>
                <input type="text" class="form-control" name="harga_modal" placeholder="cth : 5000" required>
            </div>

            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Harga Jual</dt>
                </label>
                <input type="text" class="form-control" name="harga" placeholder="cth : 5000" required>
            </div>

            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Komisi</dt>
                </label>
                <select name="komisi" class="form-control select" id="" required>

                    <option value="5">5%</option>
                    <option value="2.5">2.5%</option>
                    <option value="0">0%</option>


                </select>
            </div>




        </div>
    </div>

</div>