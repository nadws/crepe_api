<div class="card">
    <div class="card-header bg-gradient">
        <h5>Laporan Total Tkmr Sdb Majoo Stk + service charge & ppn</h5>
        <h5>{{ tanggal($tgl1) }} ~ {{ tanggal($tgl2) }}</h5>
    </div>
    <div class="card-body">
        <p><b>TAKEMORI : {{ number_format($takemori, 0) }}</b></p>
        <p><b>SOONDOBU : {{ number_format($soondobu, 0) }}</b></p>
        <p><b>TOTAL : {{ number_format($takemori + $soondobu, 0) }}</b></p>
        
    </div>
</div>