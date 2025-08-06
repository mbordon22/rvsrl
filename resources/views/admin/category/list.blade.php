<div class="cf nestable-lists">
    <div class="dd" id="nestable3">
        <ol class="dd-list">
            @if (isset($categories))
                @forelse ($categories as $category)
                    <li class="dd-item dd3-item {{ isset($cat) && $cat->id == $category->id ? 'active' : '' }}"
                        data-id="{{ $category->id }}">
                        <div class="dd-handle dd3-handle">
                            <svg>
                                <use href="{{ asset('assets/svg/icon-sprite.svg#arrow-four') }}"></use>
                            </svg>Drag</div>
                        <div class="dd3-content">{{ $category->name }}
                            <form method="POST" action="{{ route('admin.category.destroy', $category->id) }}">
                                @csrf
                                @method('DELETE')

                                <a href="#confirmationModal{{ $category->id }}" data-bs-toggle="modal"
                                    class="delete-svg">
                                    <i data-feather="trash-2" class="remove-icon delete-confirmation"></i>
                                </a>

                                <a href="{{ route('admin.category.edit', [$category->id]) }}" class="edit-icon"><i
                                        data-feather="edit"></i></a>

                                <!-- Remove category Confirmation-->
                                <div class="modal fade" id="confirmationModal{{ $category->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="confirmationModalLabel{{ $category->id }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Confirm delete</h4>
                                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><b> Are you sure want to delete ?</b></p>
                                                <p>This Item Will Be Deleted Permanently. You Can not Undo This Action.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" type="button"
                                                    data-bs-dismiss="modal">Close</button>
                                                <form action="{{ route('admin.category.destroy', $category->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger"
                                                        type="submit">{{ __('Delete') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        @if (!$category?->childs?->isEmpty())
                            @include('admin.category.childs', ['childs' => $category->childs])
                        @endif
                    </li>
                @empty
                    <div class="no-data-detail text-center">
                        <img class="img-lg" src="{{ asset('assets/svg/no-data-holder.svg') }}" alt="no-data">
                        <div class="data-not-found mt-2">
                            <h5>No Category Found</h5>
                        </div>
                    </div>
                @endforelse
            @endif
        </ol>
    </div>
</div>
