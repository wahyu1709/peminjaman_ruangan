<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Role</th>
        <th>Jabatan</th>
        <th>NIM/NIP</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->jenis_pengguna }}</td>
            <td>{{ $user->nim_nip }}</td>
            <td>{{ $user->email }}</td>
        </tr>
        @endforeach
    </tbody>
</table>