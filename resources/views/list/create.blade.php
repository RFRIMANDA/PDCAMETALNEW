@extends('layouts.main')

@section('content')

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1><span class="badge bg-warning">Create New Risk and Opportunity</span></h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('list.store', ['id' => $enchan]) }}" method="POST">
        @csrf
        <input type="hidden" name="id_divisi" value="{{ $enchan }}" required>

        <!-- Section untuk Issue -->
        <div class="form-group mb-4">
            <label for="issue" style="font-weight: 900;">Issue:</label>
            <textarea name="issue" id="issue" class="form-control" rows="3" required>{{ old('issue') }}</textarea>
            @error('issue')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Section untuk Fields (Pihak, Resiko, Tindakan, PIC) -->
        <div class="card mb-4">
            <div class="card-body">
                <div id="dynamicForm">
                    <!-- Default Entry -->
                    <div class="dynamic-entry">
                        <div class="form-group mb-4">
                            <label for="pihak" style="font-weight: 900;">Pihak yang Berkepentingan:</label>
                            <select name="pihak[]" class="form-control">
                                <option value="">-- Pilih Pihak --</option>
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                            @error('pihak')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="resiko" style="font-weight: 900;">Resiko (R):</label>
                            <textarea name="resiko[]" class="form-control" rows="3">{{ old('resiko.0') }}</textarea>
                            @error('resiko')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="tindakan" style="font-weight: 900;">Tindak Lanjut:</label>
                            <textarea name="tindakan[]" class="form-control" rows="3">{{ old('tindakan.0') }}</textarea>
                            @error('tindakan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="pic" style="font-weight: 900;">Target PIC:</label>
                            <textarea name="pic[]" class="form-control" rows="3">{{ old('pic.0') }}</textarea>
                            @error('pic')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="button" class="btn btn-danger remove-entry">Remove</button>
                        <hr class="my-4">
                    </div>
                </div>
                <button type="button" class="btn btn-success" id="addEntry">Add More</button>
                <br>
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="peluang" style="font-weight: 900;">Peluang:</label>
            <textarea name="peluang" id="peluang" class="form-control" rows="3">{{ old('peluang') }}</textarea>
            @error('peluang')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="tingkatan" style="font-weight: 900;">Tingkatan:</label>
            <select name="tingkatan" id="tingkatan" class="form-control">
                <option value="">-- Pilih Tingkatan --</option>
                <option value="HIGH">High</option>
                <option value="MEDIUM">Medium</option>
                <option value="LOW">Low</option>
            </select>
            @error('tingkatan')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="status" style="font-weight: 900;">Status:</label>
            <select name="status" id="status" class="form-control" >
                <option value="">-- Pilih Status --</option>
                <option value="OPEN" {{ old('status') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                <option value="ON PROGRESS" {{ old('status') == 'ON PROGRESS' ? 'selected' : '' }}>ON PROGRESS</option>
                <option value="CLOSE" {{ old('status') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="risk" style="font-weight: 900;">Actual Risk:</label>
            <select name="risk" id="risk" class="form-control">
                <option value="">-- Pilih Risk --</option>
                <option value="HIGH" {{ old('risk') == 'HIGH' ? 'selected' : '' }}>High</option>
                <option value="MEDIUM" {{ old('risk') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                <option value="LOW" {{ old('risk') == 'LOW' ? 'selected' : '' }}>Low</option>
            </select>
            @error('risk')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="before" style="font-weight: 900;">Before:</label>
            <textarea name="before" id="before" class="form-control" rows="3">{{ old('before') }}</textarea>
            @error('before')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="after" style="font-weight: 900;">After:</label>
            <textarea name="after" id="after" class="form-control" rows="3">{{ old('before') }}</textarea>
            @error('after')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    let dynamicForm = document.getElementById('dynamicForm');
    let addEntryButton = document.getElementById('addEntry');
    let entryCounter = 1; // Counter untuk ID unik

    addEntryButton.addEventListener('click', function() {
        let newEntry = dynamicForm.querySelector('.dynamic-entry').cloneNode(true);
        
        // Update ID dan name attributes untuk elemen baru
        newEntry.querySelectorAll('select, textarea').forEach((element) => {
            let name = element.getAttribute('name');
            if (name) {
                element.setAttribute('name', name.replace(/\[\d*\]/, `[${entryCounter}]`));
            }
        });

        newEntry.querySelectorAll('button.remove-entry').forEach((button) => {
            button.addEventListener('click', function() {
                newEntry.remove();
            });
        });

        // Add the new entry to the form
        dynamicForm.appendChild(newEntry);
        entryCounter++;
    });
});

</script>

@endsection
