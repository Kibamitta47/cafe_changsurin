@extends('layouts.admin')

@section('content')
    <h1>รายการคาเฟ่</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อคาเฟ่</th>
                    <th>ชื่อสถานที่</th>
                    <th>ที่อยู่</th>
                    <th>ช่วงราคา</th>
                    <th>ละติจูด</th>
                    <th>ลองจิจูด</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cafes as $index => $cafe)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $cafe->cafe_name }}</td>
                        <td>{{ $cafe->place_name }}</td>
                        <td>{{ $cafe->address }}</td>
                        <td>{{ $cafe->price_range }}</td>
                        <td class="text-center">{{ $cafe->lat }}</td>
                        <td class="text-center">{{ $cafe->lng }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">ไม่มีข้อมูลคาเฟ่</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
