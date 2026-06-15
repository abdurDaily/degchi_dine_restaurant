<table class="table mb-0 table-borderless">
    <tbody>
        <tr class="text-center">
            <td colspan="2">
                <img class="rounded img-fluid" src="{{ Auth::user()->profile_image }}"
                    alt="">
            </td>
        </tr>
        <tr>
            <th class="ps-0" scope="row">Name :</th>
            <td class="text-muted">{{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <th class="ps-0" scope="row">E-mail :</th>
            <td class="text-muted">{{ Auth::user()->email }}</td>
        </tr>
        <tr>
            <th class="ps-0" scope="row">User Number :</th>
            <td class="text-muted">{{ Auth::user()->user_number }}</td>
        </tr>
    </tbody>
</table>
