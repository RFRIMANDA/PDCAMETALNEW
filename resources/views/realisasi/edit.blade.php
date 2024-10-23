@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Edit Track Record {{ $tindak }}</h1>

    <form action="{{ route('realisasi.update', $realisasi->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- <input type="text" name="id_divisi" value="{{ $realisasi->id }}" readonly> --}}

        <div id="inputContainer">
            @foreach($realisasiList as $item)
            <div class="activity-section">
                <!-- Aktivitas -->
                <div class="row mb-3">
                    <label for="nama_realisasi" class="col-sm-2 col-form-label"><strong>Activity</strong></label>
                    <div class="col-sm-10">
                        <textarea name="nama_realisasi[]" class="form-control mb-2" rows="3">{{ old('nama_realisasi.' . $loop->index, $item->nama_realisasi) }}</textarea>
                    </div>
                </div>

                <!-- PIC -->
                <div class="row mb-3">
                    <label for="target" class="col-sm-2 col-form-label"><strong>PIC</strong></label>
                    <div class="col-sm-10">
                        <textarea name="target[]" class="form-control mb-2" rows="3">{{ old('target.' . $loop->index, $item->target) }}</textarea>
                    </div>
                </div>

                <!-- Noted -->
                <div class="row mb-3">
                    <label for="desc" class="col-sm-2 col-form-label"><strong>Noted</strong></label>
                    <div class="col-sm-10">
                        <textarea name="desc" class="form-control mb-2" rows="3">{{ old('desc.' . $loop->index, $item->desc) }}</textarea>
                    </div>
                </div>

                <!-- Tanggal Penyelesaian -->
                <div class="row mb-3">
                    <label for="tgl_realisasi" class="col-sm-2 col-form-label"><strong>Tanggal Penyelesaian</strong></label>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" name="tgl_realisasi[]" value="{{ old('tgl_realisasi.' . $loop->index, $item->tgl_realisasi) }}">
                    </div>
                </div>

                <!-- Status -->
                <div class="row mb-3">
                    <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
                    <div class="col-sm-3">
                        <select name="status" class="form-control">
                            <option value="ON PROGRES" {{ old('status.' . $loop->index, $item->status) == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRES</option>
                            <option value="CLOSE" {{ old('status.' . $loop->index, $item->status) == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                        </select>
                    </div>
                </div>

                <hr>
            </div>
            @endforeach
        </div>

        <!-- Tombol untuk menambah lebih banyak -->
        <button type="button" class="btn btn-secondary" id="addMore">Add More</button>

        <!-- Tombol untuk menyimpan -->
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save <i class="ri-save-3-fill"></i></button>
        </div>
    </form>
</div>

<script>
    document.getElementById('addMore').addEventListener('click', function() {
    var newInputSection = `
    <div class="activity-section">
        <hr>
        <div class="row mb-3">
            <label for="nama_realisasi" class="col-sm-2 col-form-label"><strong>Activity</strong></label>
            <div class="col-sm-10">
                <textarea name="nama_realisasi[]" class="form-control mb-2" rows="3"></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="target" class="col-sm-2 col-form-label"><strong>PIC</strong></label>
            <div class="col-sm-10">
                <textarea name="target[]" class="form-control mb-2" rows="3"></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="desc" class="col-sm-2 col-form-label"><strong>Noted</strong></label>
            <div class="col-sm-10">
                <textarea name="desc[]" class="form-control mb-2" rows="3"></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="tgl_realisasi" class="col-sm-2 col-form-label"><strong>Tanggal Penyelesaian</strong></label>
            <div class="col-sm-3">
                <input type="date" class="form-control" name="tgl_realisasi[]">
            </div>
        </div>
    </div>`;
    document.getElementById('inputContainer').insertAdjacentHTML('beforeend', newInputSection);
});
</script>

@endsection
