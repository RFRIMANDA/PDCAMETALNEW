@extends('layouts.main')

@section('content')

<section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Departement</h5>

              <!-- Table with stripped rows -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="text-align: center; vertical-align: middle;">
                      <b>N</b>o
                    </th>
                    <th style="text-align: center; vertical-align: middle;">Table Departement</th>
                    <th style="text-align: center; vertical-align: middle;">Jumlah Data</th>
                    <th style="text-align: center; vertical-align: middle;">Create Risk & Opportunity Register</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($divisi as $d)
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">{{$loop->iteration}}</td>

                        <td>
                            <a href="{{ route('riskregister.tablerisk', $d->id) }}" class="btn btn-info">
                                {{$d->nama_divisi}}
                            </a>
                        </td>


                        <td style="text-align: center; vertical-align: middle;">
                            <span class="badge bg-secondary">{{ $d->jumlah_data }} Data</span>
                            @if($d->done_count > 0)
                                <span class="badge bg-success">{{ $d->done_count }} Done</span>
                            @endif
                        </td>

                        <td style="text-align: center; vertical-align: middle;">
                            <a href="{{ route('riskregister.create', $d->id) }}" class="btn btn-warning btn-sm" title="Create List Register">
                                <i class="fas fa-plus"></i>
                            </a>
                            {{-- <a href="{{ route('riskregister.printAll', $d->id) }}" class="btn btn-success btn-sm" title="Print All Risk Register">
                                <i class="fas fa-print"></i>
                            </a> --}}
                        </td>

                    </tr>
                    @endforeach

                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

@endsection
