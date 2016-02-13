<!-- Datalist start -->
<table class='expand' id='datalist'>
    <thead>
        <tr>
            @foreach ($columns as $column)
            <th>{{$column}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr><td colspan='99'><center>Silakan pilih data.</center></td></tr>
    </tbody>
</table>
<!-- Datalist end -->