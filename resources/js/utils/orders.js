// orders.js
import { DateFormatter } from './dates';

export class OrderFormatter {
    static getStatusString(agendado, dataAgendada, horaInicio, status) {
        const dateIso = DateFormatter.dateToISOFormat(`${dataAgendada} ${horaInicio}`);
        const currentDate = new Date();
        const scheduleDate = new Date(dateIso);
        const timeDiff = (scheduleDate - currentDate) / (1000 * 60);

        const statusKey =
            agendado == 1 && currentDate < scheduleDate && timeDiff > 30
                ? 9
                : [2, 3, 4, 5].includes(status)
                    ? 2
                    : status;

        return this.getStatusConfig()[statusKey];
    }

    static getStatusConfig() {
        return {
            1: { label: "Pendente", classes: { bg: "bg-warning", text: "text-warning", icon: "ri-error-warning-fill" } },
            2: { label: "Cancelado", classes: { bg: "bg-danger", text: "text-danger", icon: "ri-close-circle-fill" } },
            // ... rest of status configurations
        };
    }
}
