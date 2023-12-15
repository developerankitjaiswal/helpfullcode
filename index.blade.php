@extends('layouts.app')
@section('content')
<div id="content" class="app-content">
    <div class="d-flex align-items-center bg-white mb-3" style="margin-top:-20px;padding:10px; margin-left:-29px; margin-right:-29px;">
        <div>
            <h1 class="page-header mb-0"><span class="btn btn-default filterButton"> <i class="fa fa-filter"></i></span>  Leads</h1>
        </div>
        @can('lead-create')
            <div class="ms-auto">
                <a href="{{ route('leads.create') }}" class="btn btn-primary px-4"><i class="fa fa-plus fa-lg ms-n2 "></i> &nbsp; Create Lead</a>
                {{-- Action Btn --}}
                <div class="btn-group">
                    <a href="javascript:;" class="btn btn-default">Actions</a>
                    <a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle" aria-expanded="false"><i class="fa fa-caret-down"></i></a>
                    <div class="dropdown-menu dropdown-menu-end" style="">
                        <a href="javascript:;" class="dropdown-item">Mass Delete</a>
                        <a href="javascript:;" class="dropdown-item">Mass Mail</a>
                        <a href="javascript:;" class="dropdown-item">Mass Update</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:;" class="dropdown-item">Print View</a>
                    </div>
                </div>
                {{-- Action Btn --}}
                <a href="#" class="btn btn-danger px-4" id="btn-delete" style="display: none;"> Delete</a>
            </div>
        @endcan
    </div>
    <div class="row">
        {{-- Filter here --}} 
        @unless($filterbox)
        <div class="col-lg-2">
            <div class="card border-0 mb-4">
                <div class="card-header h6 mb-0 bg-none p-3" style="border-top: 1px solid #2196f3;">
                    <i class="fab fa-buromobelexperte fa-lg fa-fw text-dark text-opacity-50 me-1"></i> Filter
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>
        @endunless
        {{-- table here --}} 
        <div class="{{ $filterbox ? 'col-lg-12' : 'col-lg-10' }}">
            <div class="card border-0 mb-4">
                @if (\Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-0">
                        <strong> Success! </strong> {{ \Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></span>
                  </div>
                @endif
                <div class="card-body" style="border-top: 1px solid #2196f3;">
                    <table id="data-table-default" class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th width="1%" data-orderable="false">
                                    <div class="col-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="selectcheckbox">
                                    </div>
                                </th>
                                <th class="text-nowrap" data-order="asc">Name</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">Lead Source</th>
                                <th class="text-nowrap">Lead Owner</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $lead)
                                <tr>
                                    <td width="1%">
                                        <div class="col-3 form-check">
                                            <input type="checkbox" name="lead[]" value="{{ $lead->id }}" class="form-check-input lead-checkbox">
                                        </div>
                                    </td>
                                    <td class="fw-600"><a href="{{ route('leads.show',['id' => encode_string($lead->id)]) }}"> {{ $lead->first_name }} </a></td>
                                    <td class="fw-600"><a href="{{ route('leads.show',['id' => encode_string($lead->id)]) }}">{{ $lead->email }}</a></td>
                                    <td class="fw-600">{{ $lead->mobile }}</td>
                                    <td class="fw-600">{{ $lead->lead_source }}</td>
                                    <td class="fw-600">{{ $lead->username }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- table here --}}
    </div>
</div>
@endsection
@section('custom-javascript')
<script>
    $(".default-select").select2({ minimumResultsForSearch: Infinity });
    $(".multiple-select").select2({ placeholder: "Select Role" });
    // Select All checkbox
    $('#selectcheckbox').click(function () {
        $('.lead-checkbox').prop('checked', this.checked);
        toggleDeleteButton();
    });
    // Individual checkbox
    $('.lead-checkbox').change(function () {
        toggleDeleteButton();
    });
    //show delete button
    function toggleDeleteButton() {
        var checkedCheckboxes = $('.lead-checkbox:checked');
        if (checkedCheckboxes.length > 0) {
            $('#btn-delete').show();
        } else {
            $('#btn-delete').hide();
        }
    }
    //Filter Button
    $(".filterButton").click(function () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        $.post('{{ route('leads.filterbox') }}', function (data) {
            location.reload();
        })
        .fail(function (xhr, status, error) {
            console.error("AJAX Error: " + error);
        });
    });
</script>
@endsection