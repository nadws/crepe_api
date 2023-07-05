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
    </style>
    <div class="row">
        <div class="col-lg-12">
            @php
                            $bulan_2 = ['bulan', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $bulan1 = (int) date('m');
                        @endphp
            <h1 class="ml-5">Absen : <span id="ketbul">{{ $bulan_2[$bulan1] }}</span> - <span
                id="ketah">{{ date('Y') }}</span></h1><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <select wire:model="valBulan" id="bulan" class="form-control mb-3 " name="bulan">
                <option value="">--Pilih Bulan-- </option>
                @foreach ($listBulan as $key => $value)
                    <option value="{{ $key }}" {{ (int) date('m') == $key ? 'selected' : '' }}>
                        {{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
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
        <div class="col-lg-4">
            
            <a href="{{ route('downloadAbsen', [
                'bulanDwn' => $this->valBulan,
                'tahunDwn' => $this->valTahun,
            ]) }}" target="_blank" class="btn btn-sm btn-success mb-3" href="#">
                <i class="fa fa-download"></i> DOWNLOAD
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">


            <div class="card">
                <table class="table table-md table-stripped table-bordered" width="100%">
                    <thead class="table-success">
                        <tr>

                            <th
                                style="white-space: nowrap;position: sticky;
                                left: 0;
                                z-index: 999;">
                                NAMA 
                            </th>
                            @php
                                $totalLoop = $valBulan == (int) date('m') ? (int) date('d') : cal_days_in_month(CAL_GREGORIAN, $this->valBulan, $this->valTahun);
                            @endphp
                            @for ($i = 1; $i <= $totalLoop; $i++)
                                <th class="text-center">{{ $i }}</th>
                            @endfor
                            <th>M</th>
                            <th>E</th>
                            <th>SP</th>
                            <th>OFF</th>
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

                                @for ($i = 1; $i <= $totalLoop; $i++)
                                    @php
                                        $data = DB::table('tb_absen')
                                            ->select('tb_absen.*')
                                            ->where('id_karyawan', '=', $d->id_karyawan)
                                            ->whereDay('tgl', '=', $i)
                                            ->whereMonth('tgl', '=', $valBulan)
                                            ->whereYear('tgl', '=', $valTahun)
                                            ->first();
                                    @endphp

                                    @if ($data)
                                        <td class="text-center m">
                                            @php
                                                $statusColorMap = [
                                                    'M' => 'success',
                                                    'E' => 'warning',
                                                    'SP' => 'primary',
                                                    'OFF' => 'info',
                                                ];
                                                $warna = $statusColorMap[$data->status];
                                            @endphp
                                            <div class="dropdown">
                                                <button class="btn btn-block btn-{{ $warna }} dropdown-toggle"
                                                    type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    {{ $data->status }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a wire:click="clickEdit({{ $data->id_absen }}, 'M')"
                                                        style="width:60px;"
                                                        class="btnUpdate btn text-center btn-warning mb-3">M</a>
                                                    <a wire:click="clickEdit({{ $data->id_absen }}, 'E')"
                                                        style="width:60px;"
                                                        class="btnUpdate btn text-center btn-warning mb-3">E</a>
                                                    <a wire:click="clickEdit({{ $data->id_absen }}, 'SP')"
                                                        style="width:60px;"
                                                        class="btnUpdate btn text-center btn-primary mb-3">SP</a>
                                                    <a wire:click="clickEdit({{ $data->id_absen }}, 'OFF')"
                                                        style="width:60px;"
                                                        class="btnUpdate btn text-center btn-info mb-3">OFF</a>
                                                </div>
                                            </div>

                                        </td>
                                    @else
                                        <td class="bg-info m">
                                            <a wire:click="clickOff({{ $d->id_karyawan }}, {{ $i }})"
                                                class="btnInput btn btn-block  btn-info">
                                                OFF
                                            </a>
                                        </td>
                                    @endif
                                @endfor
                                <td class="bg-light">{{ $this->getTotal($d->id_karyawan, 'M') }}</td>
                                <td class="bg-light">{{ $this->getTotal($d->id_karyawan, 'E') }}</td>
                                <td class="bg-light">{{ $this->getTotal($d->id_karyawan, 'SP') }}</td>
                                <td class="bg-light">
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
