@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Users</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">All Users</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                {{-- Nếu có chức năng thêm user thì mở comment dưới --}}
                {{-- <a class="tf-button style-1 w208" href="#"><i class="icon-plus"></i>Add new</a> --}}
            </div>
            <div class="table-responsive">
                @if(Session::has('status'))
                    <p class="alert alert-success">{{ Session::get('status') }}</p>
                @endif
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Account Type</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="product-item">
                                    
                                    <div class="name">
                                        <div class="body-text">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->utype === 'ADM' ? 'Administrator' : 'Customer' }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>
                                <div class="list-icon-function" style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.user.profile', ['id' => $user->id]) }}">
                                        <div class="item eye" title="View Profile">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}">
                                        <div class="item edit" title="Edit User" style="color: #007bff; cursor: pointer;">
                                            <i class="icon-edit"></i>
                                        </div>
                                    </a>
                                    <form action="{{ route('admin.user.delete', ['id' => $user->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="item delete" title="Delete User" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
