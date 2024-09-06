@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Edit Data ListForm</h1>

    <form action="{{ route('list.update', $form->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="id_divisi" class="form-label">Divisi</label>
            <select name="id_divisi" class="form-control" required>
                @foreach($divisi as $d)
                    <option value="{{ $d->id }}" {{ $form->id_divisi == $d->id ? 'selected' : '' }}>
                        {{ $d->nama_divisi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="issue" class="form-label">Issue</label>
            <textarea name="issue" class="form-control" rows="3" required>{{ $form->issue }}</textarea>
        </div>

        <!-- Pihak, Resiko, Tindakan, PIC Dynamic Fields -->
        <div class="card mb-4">
            <div class="card-body">
                <div id="dynamicForm">
                    @foreach (json_decode($form->pihak, true) as $index => $pihak)
                        <div class="dynamic-entry">
                            <div class="form-group mb-4">
                                <label for="pihak" class="form-label">Pihak yang Berkepentingan:</label>
                                <select name="pihak[]" class="form-control">
                                    <option value="">-- Pilih Pihak --</option>
                                    @foreach($divisi as $d)
                                        <option value="{{ $d->id }}" {{ $pihak == $d->id ? 'selected' : '' }}>
                                            {{ $d->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label for="resiko" class="form-label">Resiko:</label>
                                <textarea name="resiko[]" class="form-control">{{ json_decode($form->resiko, true)[$index] ?? '' }}</textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label for="tindakan" class="form-label">Tindakan:</label>
                                <textarea name="tindakan[]" class="form-control">{{ json_decode($form->tindakan, true)[$index] ?? '' }}</textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label for="pic" class="form-label">Target PIC:</label>
                                <textarea name="pic[]" class="form-control">{{ json_decode($form->pic, true)[$index] ?? '' }}</textarea>
                            </div>

                            <button type="button" class="btn btn-danger remove-entry">Remove</button>
                            <hr class="my-4">
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-success" id="addEntry">Add More</button>
            </div>
        </div>

        <div class="mb-3">
            <label for="peluang" class="form-label">Peluang</label>
            <textarea name="peluang" class="form-control">{{ $form->peluang }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tingkatan" class="form-label">Tingkatan</label>
            <select name="tingkatan" class="form-control">
                <option value="HIGH" {{ $form->tingkatan == 'HIGH' ? 'selected' : '' }}>High</option>
                <option value="MEDIUM" {{ $form->tingkatan == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                <option value="LOW" {{ $form->tingkatan == 'LOW' ? 'selected' : '' }}>Low</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="OPEN" {{ $form->status == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                <option value="ON PROGRESS" {{ $form->status == 'ON PROGRESS' ? 'selected' : '' }}>ON PROGRESS</option>
                <option value="CLOSE" {{ $form->status == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="risk" class="form-label">Risk</label>
            <select name="risk" class="form-control">
                <option value="HIGH" {{ $form->risk == 'HIGH' ? 'selected' : '' }}>HIGH</option>
                <option value="MEDIUM" {{ $form->risk == 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                <option value="LOW" {{ $form->risk == 'LOW' ? 'selected' : '' }}>LOW</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let dynamicForm = document.getElementById('dynamicForm');
        let addEntryButton = document.getElementById('addEntry');

        addEntryButton.addEventListener('click', function() {
            let newEntry = dynamicForm.querySelector('.dynamic-entry').cloneNode(true);
            newEntry.querySelectorAll('input, textarea, select').forEach(function(input) {
                input.value = '';
            });
            dynamicForm.appendChild(newEntry);
            addRemoveButtonListener(newEntry.querySelector('.remove-entry'));
        });

        function addRemoveButtonListener(button) {
            button.addEventListener('click', function() {
                if (dynamicForm.querySelectorAll('.dynamic-entry').length > 1) {
                    button.closest('.dynamic-entry').remove();
                }
            });
        }

        dynamicForm.querySelectorAll('.remove-entry').forEach(function(button) {
            addRemoveButtonListener(button);
        });
    });
</script>

@endsection
