<?php

namespace Kanboard\Core\ICalendar;

/**
 * iCalendar VTODO component for a to-do without start or due date
 *
 * @package core
 * @author  Frederic Guillot
 */
final class Todo extends Component
{
    protected function name(): string
    {
        return 'VTODO';
    }

    protected function leadingLines(): array
    {
        return ['STATUS:NEEDS-ACTION'];
    }
}
