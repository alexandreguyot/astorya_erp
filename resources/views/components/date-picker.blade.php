<div wire:ignore>
    <div class="datepicker-container datepicker-{{ $attributes['id'] }} relative">
        <a class="input-button" title="toggle" data-toggle>
            <i class="icon-calendar"></i>
        </a>
        <input type="text" class="form-control" {{ $attributes }} autocomplete="off">
    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
    @endpush
@endonce

@once
    @push('scripts')
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
    @endpush
@endonce

@pushOnce('scripts')
    <script>
        $(function() {
            $.datepicker.regional['fr'] = {
                closeText: 'Fermer',
                prevText: 'Précédent',
                nextText: 'Suivant',
                currentText: 'Aujourd\'hui',
                monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                ],
                monthNamesShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin',
                    'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'
                ],
                dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                weekHeader: 'Sem',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['fr']);

            // Traduction en français
            $.timepicker.regional['fr'] = {
                timeOnlyTitle: 'Choisir une heure',
                timeText: 'Heure',
                hourText: 'Heures',
                minuteText: 'Minutes',
                secondText: 'Secondes',
                currentText: 'Maintenant',
                closeText: 'Fermer',
                timeFormat: 'HH:mm',
                isRTL: false
            };
            $.timepicker.setDefaults($.timepicker.regional['fr']);
        });
    </script>
@endpushOnce


@push('scripts')
<script>
    document.addEventListener("livewire:load", () => {
        function update(value) {
            let el = document.getElementById('clear-{{ $attributes['id'] }}');

            if (value === '' || (Array.isArray(value) && value.length === 0)) {
                value = '';
                if (el !== null) {
                    el.classList.add('invisible');
                }
            } else if (el !== null) {
                el.classList.remove('invisible');
            }

            @this.set('{{ $attributes['wire:model'] }}', value);
        }
        @if($attributes['picker'] === 'date')
            $('.datepicker-{{ $attributes['id'] }} input').datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                onSelect: function(dateText) {
                    update(dateText);
                },
                onClose: function() {
                    update($('.datepicker-{{ $attributes['id'] }} input').val());
                },
                onChangeMonthYear: function(year, month) {
                    let formattedDate = '01' + '/' +(month.toString().padStart(2, '0')) + '/' + year;
                    $('.datepicker-{{ $attributes['id'] }} input').val(formattedDate);
                    update(formattedDate);
                }
            });
        @elseif($attributes->has('picker') && $attributes['picker'] === 'month')
            $('.datepicker-{{ $attributes['id'] }} input').datepicker({
                dateFormat: 'mm/yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) {
                    let month = inst.selectedMonth + 1;
                    let year = inst.selectedYear;
                    let formattedDate = (month < 10 ? '0' + month : month) + '/' + year;
                    $('.datepicker-{{ $attributes['id'] }} input').val(formattedDate);
                    update(formattedDate);
                },
                beforeShow: function(input, inst) {
                    $(input).datepicker('widget').addClass('month-year-picker');
                }
            });
        @elseif($attributes->has('picker') && $attributes['picker'] === 'time')
            $('.datepicker-{{ $attributes['id'] }} input').timepicker({
                timeFormat: 'HH:mm',
                showSecond: false,
                controlType: 'select',
                oneLine: true,
                onSelect: function(time) {
                    update(time);
                }
            });

        @elseif($attributes['picker'] === 'multiple')
            let wireModelData = @this.get('{{ $attributes['wire:model'] }}');
            let dataArray = Array.isArray(wireModelData) ? wireModelData : Object.values(wireModelData);

            $('.datepicker-{{ $attributes['id'] }} input').datepicker({
                dateFormat: "dd/mm/yy",
                onSelect: function(dateText) {
                    let selectedDates = $(this).val().split(',');
                    selectedDates.sort((a, b) => new Date(b) - new Date(a));
                    @this.set('{{ $attributes["wire:model"] }}', selectedDates);
                },
                beforeShow: function(input, inst) {
                    setTimeout(() => {
                        $(inst.dpDiv).addClass('multiple-datepicker');
                    }, 0);
                }
            });

            $('.datepicker-{{ $attributes['id'] }} input').val(dataArray.join(', '));

        @else
            $('.datepicker-{{ $attributes['id'] }} input').datetimepicker({
                dateFormat: "dd/mm/yy",
                timeFormat: "HH:mm",
                controlType: 'select',
                oneLine: true,
                onSelect: function(dateText) {
                    update(dateText);
                },
                onClose: function() {
                    update($('.datepicker-{{ $attributes['id'] }} input').val());
                }
            });
        @endif
    });
</script>
@endpush
