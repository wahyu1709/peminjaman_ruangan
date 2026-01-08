<h1 align='center'>Data User</h1>
<hr>
<table width='100%' border="1px" style="border-collapse:collapse">
    <thead>
    <tr>
        <th width='20'>No</th>
        <th width='20'>Nama</th>
        <th width='20'>Role</th>
        <th width='20'>Jabatan</th>
        <th width='20'>NIM/NIP</th>
        <th width='20'>Email</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td align="center">{{ $loop->iteration }}</td>
            <td align="center">{{ $user->name }}</td>
            <td align="center">{{ $user->role }}</td>
            <td align="center">{{ $user->jenis_pengguna }}</td>
            <td align="center">{{ $user->nim_nip }}</td>
            <td align="center">{{ $user->email }}</td>
        </tr>
        @endforeach
    </tbody>
</table>