{{--
    Скрипты единого модуля контактов.

    Параметры:
    - $context (string, required): 'properties' | 'companies' | 'complexes' | 'developers'
    - $maxContacts (int, default 0): лимит контактов (0 = без лимита)
    - $behavior (array, default []): переопределение Config.behavior
--}}

@php
    $maxContacts = $maxContacts ?? 0;
    $behavior = $behavior ?? [];
@endphp

<script>
    window.ContactModal = window.ContactModal || {};
    window.ContactModal.configure({
        context: '{{ $context }}',
        maxContacts: {{ $maxContacts }},
        behavior: {!! json_encode((object) $behavior) !!}
    });
</script>
<script src="{{ asset('js/shared/contact-modal/config.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/utils.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/components.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/api.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/form.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/contact-list.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/handlers.js') }}"></script>
<script src="{{ asset('js/shared/contact-modal/main.js') }}"></script>
