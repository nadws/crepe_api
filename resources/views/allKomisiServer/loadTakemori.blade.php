
<div class="card">
    <div class="card-header">

        <h5 style="font-weight: bold" class="text-center">
            Takemori
        </h5>
        <h5>Org p/r : {{ number_format($jumlah_orang->jumlah, 0) }} /
            {{ number_format($orang, 0) }} </h5>

        <h5>Service charge p/r :
            {{ number_format(($service_charge / 7) * $persen->jumlah_persen, 0) }} /
            {{ number_format($kom, 0) }}</h5>

    </div>

    <div class="card-body">
        @php
            $ttl_komisi = 0;
            $total_m = 0;
            $total_sp = 0;
            foreach ($server as $k) {
                if($k->point != 'Y') {
                continue;
                } else {
                $ttl_komisi += $k->komisi;
                $total_m += $k->qty_m + $k->qty_e;
                $total_sp += $k->qty_sp * 2;
                }
            }
        @endphp

        <table class="table" id="table"
            style="font-size: 14px">
            <thead style="white-space: nowrap; ">
                <tr>
                    <th>#</th>
                    <th style="font-size: 10px;text-align: center">Nama</th>
                    <th style="font-size: 10px;text-align: center">M</th>
                    <th style="font-size: 10px;text-align: center">E</th>
                    <th style="font-size: 10px;text-align: center">SP</th>
                    <!--<th style="font-size: 10px;text-align: right">Total Penjualan <br>-->
                    <!--    ({{ number_format($ttl_komisi, 0) }}) </th>-->
                    <th style="font-size: 10px;text-align: right">Kom Penjualan {{$total_m}} / {{$total_sp}}</th>
                    <th style="font-size: 10px;text-align: right">Kom Stk</th>
                    <th style="font-size: 10px;text-align: right">Kom Majo</th>
                    <th style="font-size: 10px;text-align: right">Kom KPI</th>
                    <th style="font-size: 10px;text-align: right">Total Gaji</th>
                    <th style="font-size: 10px;text-align: right">Total Komisi</th>
                    <th style="font-size: 10px;text-align: right">Grand Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($server as $no => $k)
                    <tr>
                        <td>
                            {{ $no + 1 }}
                        </td>
                        <td>
                            {{ $k->nama }}
                        </td>
                        <td>
                            {{ $k->qty_m }}
                        </td>
                        <td>
                            {{ $k->qty_e }}
                        </td>
                        <td>
                            {{ $k->qty_sp }} 
                        </td>
                        <!--<td style="text-align: right">-->
                        <!--    {{ number_format($k->komisi, 0) }}-->
                        <!--</td>-->
                        @php
                            $absen_m = $k->qty_m + $k->qty_e;
                            $absen_sp = $k->qty_sp * 2;
                            $kom1 = $ttl_kom == '' ? '0' : ($kom / $bagi_kom) * $k->komisi;
                        @endphp
                        <!--<td style="text-align: right">-->
                        <!--    {{ number_format($kom1, 0) }} / {{$total_sp}}-->
                        <!--</td>-->
                        <td style="text-align: right">
                            {{ $k->point != 'Y' ? '0' : number_format(($kom / ($total_m + $total_sp)) * ($absen_m + $absen_sp),0)  }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format(round($k->kom, 0), 0) }}
                        </td>
                        @php
                            $komisiG = Http::get("https://majoo.ptagafood.com/api/komisiGaji/1/$k->karyawan_majo/$tgl1/$tgl2");
                            $komaj = empty($komisiG['komisi']) ? 0 : $komisiG['komisi'][0]['dt_komisi'];
                        @endphp 
                        <td style="text-align: right">
                            {{ number_format($komaj, 0) }}
                        </td>
                        <td style="text-align: right">
                            @php
                                $ttlRp = $kom * $persenBagi + $kom2 * $persenBagi;
                                $pointR = $ttlRp / $settingOrang;
                                $ttlPointRp = $pointR / 10;
                                $komKpi = $pointR - $ttlPointRp * $k->ttl;
                            @endphp
                            {{ number_format($komKpi, 0) }}
                        </td>
                        @php
                        $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
                        $ttlAllKom = $kom1 + round($k->kom, 0) + $komaj + $komKpi;
                        $grandTotal = $gaji + $ttlAllKom;
                        @endphp
                        <td style="text-align: right">{{number_format($gaji,0)}}</td>
                        <td style="text-align: right">{{number_format($ttlAllKom, 0)}}</td>
                        <td style="text-align: right">{{number_format($grandTotal, 0)}}</td>

                    </tr>
                @endforeach

            </tbody>

        </table>
    </div>

</div>