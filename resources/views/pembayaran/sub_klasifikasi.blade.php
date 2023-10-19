<table>
    @foreach ($akun_pembayaran as $a)
        <tr>
            <td></td>
            <td>{{ $a->nm_akun }}</td>
            <td>
                <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="#" class="btn btn-danger btn-sm delete_akun"
                    id_akun_pembayaran="{{ $a->id_akun_pembayaran }}" id_klasifikasi="{{ $a->id_klasifikasi }}"><i
                        class="fas fa-trash-alt"></i></a>
            </td>
        </tr>
    @endforeach

</table>
