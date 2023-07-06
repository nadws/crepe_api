<div>
    <style>
        th {
            position: sticky;
            top: 0;
            z-index: 998;
        }

        .scrl {
            overflow: auto;
        }

        .hover {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .hover:hover {
            opacity: 1;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            @php
                $bulan_2 = ['bulan', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $bulan1 = (int) date('m');
            @endphp
            <h1 class="ml-5">Absen : <span id="ketbul">{{ $bulan_2[$this->valBulan] }}</span> - <span
                    id="ketah">{{ $this->valTahun }}</span></h1><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <label for="">Bulan</label>
            <select wire:model="valBulan" id="bulan" class="form-control mb-3 " name="bulan">
                <option value="">--Pilih Bulan-- </option>
                @foreach ($listBulan as $key => $value)
                    <option value="{{ $key }}" {{ (int) date('m') == $key ? 'selected' : '' }}>
                        {{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <label for="">Tahun</label>
            <select wire:model="valTahun" id="tahun" class="form-control mb-3 " name="tahun">
                <option value="">--Pilih Tahun--</option>
                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                @for ($i = date('Y'); $i <= date('Y') + 3; $i++)
                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <label for="">Posisi</label>
            <select wire:model="valPosisi" class="form-control mb-3" name="tahun">
                <option value="">--Pilih Posisi--</option>
                @foreach ($posisi as $p)
                    <option {{$valPosisi == $p->id_status ? 'selected' : ''}} value="{{ $p->id_status }}">{{ $p->nm_status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label for="">Pencarian</label>
            <input autofocus type="text" class="form-control" wire:model="search" placeholder="ketik nama">
        </div>
        <div class="col-lg-4">
            <label for="">Aksi</label> <br>
            <a href="{{ route('downloadAbsen', [
                'bulanDwn' => $this->valBulan,
                'tahunDwn' => $this->valTahun,
            ]) }}"
                target="_blank" class="btn btn-sm btn-success mb-3" href="#">
                <i class="fa fa-download"></i> DOWNLOAD
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">


            <div class="card" x-data="{
                open: true
            }">
                <table class="table table-stripped table-bordered" width="100%">
                    <thead class="table-success">
                        <tr>

                            <th width="5%"
                                style="white-space: nowrap;position: sticky;
                                left: 0;
                                z-index: 999;">
                                NAMA
                                {{-- <button @click="open = ! open"
                                    class="hover btn btn-sm btn-info float-right">open</button> --}}
                                <button wire:click="open"
                                    class="hover btn btn-sm btn-info float-right">open</button>
                            </th>
                            @php
                                $totalLoop = $valBulan == (int) date('m') ? (int) date('d') : cal_days_in_month(CAL_GREGORIAN, $this->valBulan, $this->valTahun);
                            @endphp
                            @for ($i = $openVal; $i <= $totalLoop; $i++)
                                <th width="2%" class="text-center" x-show="open">{{ $i }}</th>
                            @endfor
                            <th width="3%" class="text-center">M</th>
                            <th width="3%" class="text-center">E</th>
                            <th width="3%" class="text-center">SP</th>
                            <th width="3%" class="text-center">OFF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawan as $d)
                            <tr>
                                <td class="bg-dark"
                                    style="white-space: nowrap;position: sticky;
                                    left: 0;
                                    z-index: 999;">
                                    <h5>{{ $d->nm_karyawan }} </h5>
                                </td>

                                @for ($i = $openVal; $i <= $totalLoop; $i++)
                                    @php
                                        $data = DB::table('tb_absen')
                                            ->select('status', 'id_absen')
                                            ->where('id_karyawan', '=', $d->id_karyawan)
                                            ->whereDay('tgl', '=', $i)
                                            ->whereMonth('tgl', '=', $valBulan)
                                            ->whereYear('tgl', '=', $valTahun)
                                            ->first();
                                    @endphp

                                    @if ($data)
                                        <td class="text-center m" x-show="open">
                                            @php
                                                $statusColorMap = [
                                                    'M' => 'success',
                                                    'E' => 'warning',
                                                    'SP' => 'primary',
                                                    'OFF' => 'info',
                                                ];
                                                $warna = $statusColorMap[$data->status];
                                            @endphp
                                            @if ($i < date('d') || $valBulan != (int) date('m'))
                                                <button disabled class="btn btn-block btn-{{ $warna }}">
                                                    {{ $data->status }}
                                                </button>
                                            @else
                                                <div class="dropdown">
                                                    <button
                                                        class="btn btn-block btn-{{ $warna }} dropdown-toggle"
                                                        type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        {{ $data->status }}
                                                    </button>
                                                    <div class="dropdown-menu text-center"
                                                        aria-labelledby="dropdownMenuButton">
                                                        <button type="button" wire:click="clickEdit({{ $data->id_absen }}, 'M')"
                                                            style="width:60px;"
                                                            class="btn text-center btn-success mb-3">M</button>
                                                        <button type="button" wire:click="clickEdit({{ $data->id_absen }}, 'E')"
                                                            style="width:60px;"
                                                            class="btn text-center btn-warning mb-3">E</button>
                                                        <button type="button" wire:click="clickEdit({{ $data->id_absen }}, 'SP')"
                                                            style="width:60px;"
                                                            class="btn text-center btn-primary mb-3">SP</button>
                                                        <button type="button" wire:click="clickEdit({{ $data->id_absen }}, 'OFF')"
                                                            style="width:60px;"
                                                            class="btn text-center btn-info mb-3">OFF</button>
                                                    </div>
                                                </div>
                                            @endif

                                        </td>
                                    @else
                                        <td class="text-center m" x-show="open">
                                            <button
                                                {{ $i < date('d') || $valBulan != (int) date('m') ? 'disabled' : '' }}
                                                wire:click="clickOff({{ $d->id_karyawan }}, {{ $i }})"
                                                class="btn btn-block btn-info">
                                                OFF
                                            </button>
                                        </td>
                                    @endif
                                @endfor
                                <td class="bg-light" align="center">{{ $this->getTotal($d->id_karyawan, 'M') }}</td>
                                <td class="bg-light" align="center">{{ $this->getTotal($d->id_karyawan, 'E') }}</td>
                                <td class="bg-light" align="center">{{ $this->getTotal($d->id_karyawan, 'SP') }}</td>
                                <td class="bg-light" align="center">
                                    {{ $totalLoop - ($this->getTotal($d->id_karyawan, 'M') + $this->getTotal($d->id_karyawan, 'E') + $this->getTotal($d->id_karyawan, 'SP')) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div x-data="{
                    observe() {
                        const observer = new IntersectionObserver((karyawan) => {
                            karyawan.forEach(d => {
                                if (d.isIntersecting) {
                                    @this.loadMore()
                                }
                            })
                        })
                        observer.observe(this.$el)
                    }
                }" x-init="observe">
                    <div wire:loading wire:target="loadMore" class="p-1">
                        <button class="btn btn-primary" type="button" disabled="">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            Processing...
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
