<div class="card">
    <div class="card-header">
        <a href="{{ route('excel_cek_invoice', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="btn btn-sm btn-info float-right"><i class="fas fa-file-excel"></i> Export</a>
        <a href="{{ route('print_cek_invoice', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="btn btn-sm btn-info float-right mr-2"><i class="fas fa-print"></i> Print</a>
    </div>
    <div class="card-body">
        <table width="100%" class="table table-bordered" id="table_cek">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>No Nota</th>
                    <th>Ttl Rp</th>
                    @foreach ($pembayaran as $p)
                        <th>{{ $p->nm_akun }} {{ $p->nm_klasifikasi }}</th>
                    @endforeach
                    <th class="text-right">Total Rp</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice as $no => $i)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($i->tgl)) }}</td>
                        <td>{{ $i->no_nota }}</td>
                        <td>{{ number_format($i->nominal, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<script src="{{ asset('assets') }}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

<script>
    $('#table_cek').DataTable({

        "bSort": true,
        // "scrollX": true,
        "paging": true,
        "stateSave": true,
        "scrollCollapse": true
    });
</script>
