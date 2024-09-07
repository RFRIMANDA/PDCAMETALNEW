@extends('layouts.main')

@section('content')

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1><span class="badge bg-info">Table List Register TML</span></h1>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('list.tablelist') }}" method="GET">
        <!-- Tambahkan @csrf jika Anda menggunakan metode POST di tempat lain -->
        <div class="form-group mb-4">
            <label for="id_divisi" style="font-weight: 900;">Silahkan Pilih Divisi:</label>
            <div class="custom-field-wrapper">
                <select name="id_divisi" id="id_divisi" class="form-control custom-field">
                    <option value="">-- Select Divisi --</option>
                    @foreach($divisi as $d)
                        <option value="{{ $d->id }}" {{ (isset($selectedDivisi) && $selectedDivisi == $d->id) ? 'selected' : '' }}>
                            {{ $d->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="status" style="font-weight: 900;">Silahkan Pilih Status:</label>
            <div class="custom-field-wrapper">
                <select name="status" id="status" class="form-control custom-field">
                    <option value="">-- Select Status --</option>
                    <option value="OPEN" {{ (isset($selectedStatus) && $selectedStatus == 'OPEN') ? 'selected' : '' }}>OPEN</option>
                    <option value="ON PROGRESS" {{ (isset($selectedStatus) && $selectedStatus == 'ON PROGRESS') ? 'selected' : '' }}>ON PROGRESS</option>
                    <option value="CLOSE" {{ (isset($selectedStatus) && $selectedStatus == 'CLOSE') ? 'selected' : '' }}>CLOSE</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">No.</th>
                    <th style="width: 200px;">Issue</th>
                    <th style="width: 200px;">Pihak Berkepentingan</th>
                    <th style="width: 200px;">Resiko (R)</th>
                    <th style="width: 200px;">Peluang (P)</th>
                    <th style="width: 100px;">Tingkatan</th>
                    <th style="width: 200px;">Tindak Lanjut</th>
                    <th style="width: 200px;">Target PIC</th>
                    <th style="width: 150px;">Status</th>
                    <th style="width: 100px;">Actual Risk</th>
                    <th style="width: 150px;">Action</th>
                    <th style="width: 200px;">Last Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                    <tr>
                        <td>{{ $form->id }}</td>
                        <td>{{ $form->issue }}</td>
                        
                        <td>
                            @if(is_array($form->pihak_names) && count($form->pihak_names) > 0)
                                @foreach($form->pihak_names as $pihakName)
                                    <span>{{ $pihakName }}</span><br>
                                    <hr style="border: 1px solid #000; margin: 5px 0;">
                                @endforeach
                            @else
                                <span>Unknown</span>
                            @endif
                        </td>

                        <td>
                            @if(!empty($form->resiko))
                                @foreach(explode(',', $form->resiko) as $resikoItem)
                                    <span>{{ trim($resikoItem) }}</span><br>
                                    <hr style="border: 1px solid #000; margin: 5px 0;">
                                @endforeach
                            @else
                                <span>Unknown</span>
                            @endif
                        </td>

                        <td>{{ $form->peluang }}</td>
                        <td>{{ $form->tingkatan }}</td>

                        <td>
                            @if(!empty($form->tindakan))
                                @foreach(explode(',', $form->tindakan) as $tindakanItem)
                                    <div>
                                        <a href="/tindakan/{{ trim($tindakanItem) }}" class="btn btn-link btn-sm" style="padding: 0;">
                                            {{ trim($tindakanItem) }}
                                        </a><br>
                                        <a href="/breakdown" class="btn btn-link btn-sm" style="padding: 0; margin-top: 5px;">
                                            <i class="ri-wallet-fill"></i> Eject
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <span>Unknown</span>
                            @endif
                        </td>

                        <td>
                            @if(!empty($form->pic))
                                @foreach(explode(',', $form->pic) as $picItem)
                                    <span>{{ trim($picItem) }}</span><br>
                                    <hr style="border: 1px solid #000; margin: 5px 0;">
                                @endforeach
                            @else
                                <span>Unknown</span>
                            @endif
                        </td>

                        <td>{{ $form->status }}</td>
                        <td>{{ $form->risk }}</td>
                        <td class="action-col">
                            <a href="{{ route('list.edit', $form->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" class="btn btn-success btn-sm">Print</a>
                        </td>
                        <td>{{ $form->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .table {
        table-layout: fixed;
    }
    .table th, .table td {
        max-width: 200px;
        overflow: hidden;
        word-wrap: break-word;
        white-space: normal;
    }
    .sticky-header {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }
    .sticky-cell {
        position: -webkit-sticky;
        position: sticky;
        background: #fff;
        z-index: 1;
    }
    .wrap-text {
        word-wrap: break-word;
        white-space: normal;
    }
</style>

@endsection
