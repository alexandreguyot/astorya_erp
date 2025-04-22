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
        <style>
            .ui-datepicker-calendar {
                display: none;
            }
            .ui-datepicker .ui-datepicker-buttonpane button {
                margin: 0 5px;
            }
            .ui-datepicker-month, .ui-datepicker-year {
                display: inline-block;
                width: auto;
            }
            .ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year {
                width: 50%;
            }
        </style>
    @endpush
@endonce

@once
    @push('scripts')
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
                monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
                monthNamesShort: ['Jan','Fév','Mars','Avril','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'],
                dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
                dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
                weekHeader: 'Sem',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['fr']);
        });
    </script>
@endpushOnce

@push('scripts')
<script>
    document.addEventListener("livewire:load", () => {
        let selector = '.datepicker-{{ $attributes["id"] }} input';
        let input = $(selector);

        function update(value) {
            @this.set('{{ $attributes["wire:model"] }}', value);
        }

        input.datepicker({
            dateFormat: 'mm/yy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            onClose: function(dateText, inst) {
                let month = inst.selectedMonth + 1;
                let year = inst.selectedYear;
                let formattedDate = (month < 10 ? '0' + month : month) + '/' + year;
                input.val(formattedDate);
                update(formattedDate);
            },
            beforeShow: function(input, inst) {
                $(input).datepicker('widget').addClass('month-year-picker');
            }
        });
    });
</script>
@endpush
