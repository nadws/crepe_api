<div class="row">
    <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
    <input type="hidden" name="keyword" value="{{ $keyword }}">

    <div class="col-sm-4 ol-md-6 col-xs-12 mb-2">
        <input type="hidden" name="id_menu" value="{{ $menu->id_menu }}">
        <label for="">Image</label>
        <br>
        <img width="270" src="https://upperclassindonesia.com/uploads/tb_menu/CHAWAN MUSHI 1.jpg" alt="">
        <br>
        <br>
        <input type="file" class="form-control" name="image">
        <input type="hidden" class="form-control" name="image2" value="CHAWAN MUSHI 1.jpg">
    </div>
    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-6 mb-2">
                <label for="">
                    <dt>Kategori</dt>
                </label>
                <select name="id_kategori" id="" class="form-control select">
                    @foreach ($kategori as $p)
                        <option value="{{ $p->kd_kategori }}"
                            {{ $p->kd_kategori == $menu->id_kategori ? 'selected' : '' }}>
                            {{ $p->kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <label for="">
                    <dt>Level Point</dt>
                </label>
                <select name="id_handicap" id="" class="form-control select">
                    <option value="">-Pilih Level-</option>
                    @foreach ($handicap as $ha)
                        <option {{ $ha->id_handicap == $menu->id_handicap ? 'selected' : '' }}
                            value="{{ $ha->id_handicap }}">{{ $ha->handicap }} ({{ $ha->point }} Point)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 mb-2">
                <label for="">
                    <dt>Kode Menu</dt>
                </label>
                <input type="text" readonly name="kd_menu" class="form-control" placeholder="Kode Menu"
                    value="{{ $menu->kd_menu }}">
            </div>
            <div class="col-lg-6 mb-2">
                <label for="">
                    <dt>Nama Menu</dt>
                </label>
                <input type="text" name="nm_menu" class="form-control" placeholder="Nama Menu"
                    value="{{ $menu->nm_menu }}">
            </div>
            <div class="col-lg-2 mb-2">
                <label for="">
                    <dt>Tipe</dt>
                </label>
                <select class="form-control select" name="tipe">
                    <option {{ $menu->tipe == 'food' ? 'selected' : '' }} value="food">food</option>
                    <option {{ $menu->tipe == 'drink' ? 'selected' : '' }} value="drink">drink</option>
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label for="">
                    <dt>Station</dt>
                </label>
                <Select class="form-control select" name="id_station">
                    <option value="">-Pilih station-</option>
                    @php
                        $st = DB::table('tb_station')
                            ->where('id_lokasi', Request::get('id_lokasi'))
                            ->orderBy('id_station', 'ASC')
                            ->get();
                    @endphp
                    @foreach ($st as $s)
                        <option {{ $s->id_station == $menu->id_station ? 'selected' : '' }}
                            value="{{ $s->id_station }}">{{ $s->nm_station }}</option>
                    @endforeach
                </Select>
            </div>
            @php
                $harga = DB::table('tb_harga')
                    ->select('tb_harga.*', 'tb_distribusi.*')
                    ->join('tb_distribusi', 'tb_harga.id_distribusi', '=', 'tb_distribusi.id_distribusi')
                    ->where('id_menu', $menu->id_menu)
                    ->get();
                $no = 1;
                
            @endphp
            @foreach ($harga as $h)
                <div class="col-lg-5 mb-2">
                    <label for="">
                        <input type="hidden" value="{{ $h->id_harga }}" name="id_harga[]">
                        <dt>Distribusi</dt>
                    </label>
                    <select name="id_distribusi[]" id="" class="form-control select">
                        @foreach ($distribusi as $d)
                            @if ($h->id_distribusi == $d->id_distribusi)
                                <option selected value="{{ $h->id_distribusi }}">
                                    {{ $h->nm_distribusi }}
                                </option>
                            @else
                                <option value="{{ $d->id_distribusi }}">
                                    {{ $d->nm_distribusi }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-5 mb-2">
                    <label for="">
                        <dt>Harga</dt>
                    </label>
                    <input type="text" name="harga[]" class="form-control" placeholder="Harga"
                        value="{{ $h->harga }}">
                </div>
            @endforeach

        </div>
    </div>
</div>
