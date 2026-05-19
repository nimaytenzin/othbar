@props([
    'id',
    'title',
    'message' => null,
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'confirmVariant' => 'primary',
    'confirmTarget' => null,
    'confirmLoadingTarget' => null,
])

@php
    $confirmButtonClass = match ($confirmVariant) {
        'success' => 'oth-btn--success',
        'danger' => 'oth-btn--danger',
        default => 'oth-btn--primary',
    };
@endphp

<dialog id="{{ $id }}" class="oth-proof-dialog oth-confirm-dialog"
    onclick="if (event.target === this) this.close()">
    <div class="oth-proof-dialog__inner" onclick="event.stopPropagation()">
        <div class="oth-dialog__header">
            <h4 class="oth-dialog__title">{{ $title }}</h4>
            <button type="button" class="oth-dialog__close"
                onclick="document.getElementById(@js($id)).close()"
                aria-label="Close">&times;</button>
        </div>

        @if($message || ! $slot->isEmpty())
            <div class="oth-dialog__body oth-dialog__body--confirm">
                @if($message)
                    <p class="oth-dialog__message">{{ $message }}</p>
                @endif
                {{ $slot }}
            </div>
        @endif

        <div class="oth-dialog__footer">
            <button type="button" class="oth-btn oth-btn--secondary oth-btn--sm"
                onclick="document.getElementById(@js($id)).close()">
                {{ $cancelLabel }}
            </button>
            <button type="button"
                @if($confirmTarget)
                    wire:click="{{ $confirmTarget }}"
                    wire:loading.attr="disabled"
                @endif
                onclick="document.getElementById(@js($id)).close()"
                class="oth-btn {{ $confirmButtonClass }} oth-btn--sm">
                @if($confirmLoadingTarget)
                    <span wire:loading.remove wire:target="{{ $confirmLoadingTarget }}">{{ $confirmLabel }}</span>
                    <span wire:loading wire:target="{{ $confirmLoadingTarget }}">Working…</span>
                @else
                    {{ $confirmLabel }}
                @endif
            </button>
        </div>
    </div>
</dialog>
