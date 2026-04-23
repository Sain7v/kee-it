<x-app-layout>

<x-slot name="header">
    <h1 class="text-lg sm:text-xl font-bold text-gray-900">Calendario de tareas</h1>
</x-slot>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
.fc .fc-button-primary { background-color: #2D6A4F; border-color: #235941; }
.fc .fc-button-primary:hover { background-color: #235941; border-color: #1a4532; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #1a4532; }
.fc .fc-daygrid-event { border-radius: 4px; font-size: 11px; padding: 2px 4px; }
.fc-event.priority-critica { background-color: #ef4444 !important; border-color: #dc2626 !important; }
.fc-event.priority-alta    { background-color: #f97316 !important; border-color: #ea580c !important; }
.fc-event.priority-media   { background-color: #eab308 !important; border-color: #ca8a04 !important; color: #1c1917 !important; }
.fc-event.priority-baja    { background-color: #22c55e !important; border-color: #16a34a !important; }

@media (max-width: 640px) {
    .fc .fc-toolbar { flex-wrap: wrap; gap: 6px; }
    .fc .fc-toolbar-title { font-size: 1rem; }
    .fc .fc-button { padding: 4px 8px; font-size: 12px; }
    .fc .fc-daygrid-event { font-size: 10px; padding: 1px 3px; }
    .fc .fc-col-header-cell { font-size: 11px; }
    .fc .fc-daygrid-day-number { font-size: 12px; }
}
</style>
@endpush

<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4 text-xs font-medium">
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-red-500"></span> Crítica</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-orange-500"></span> Alta</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-yellow-400"></span> Media</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-green-500"></span> Baja</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-2 sm:p-4 overflow-hidden">
        <div id="calendar"></div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isMobile = window.innerWidth < 640;

    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: isMobile ? 'dayGridWeek' : 'dayGridMonth',
        locale: 'es',
        headerToolbar: isMobile
            ? { left: 'prev,next', center: 'title', right: 'today' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek' },
        height: 'auto',
        events: function (info, successCallback, failureCallback) {
            fetch(`/api/tasks/calendar?start=${info.startStr}&end=${info.endStr}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(successCallback)
            .catch(failureCallback);
        },
        eventClick: function (info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        eventDidMount: function (info) {
            info.el.title = info.event.title;
        },
    });

    calendar.render();
});
</script>
@endpush

</x-app-layout>
