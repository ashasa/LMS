<div class="col-lg-12 text-center">
    {{ $curUserLeaves->links() }}
</div>

<table class="table table-hover">
    <thead>
        <tr>
            @if($showAll === true)
                <th>Emp. Name</th>
            @endif
            <th>From date</th>
            <th>To date</th>
            <th>Reason</th>
            <th>Backup Employee</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($curUserLeaves as $item)
            <tr>
                @if($showAll === true)
                    <td>{{ $item->appliedEmp->name }}</td>
                @endif
                <td>{{ $item->from_date }}</td>
                <td>{{ $item->to_date }}</td>
                <td>{{ $item->reason }}</td>
                <td>{{ $item->backupEmp->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>