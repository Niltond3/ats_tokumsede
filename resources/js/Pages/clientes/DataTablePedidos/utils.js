
export const getStatusString = (agendado, dataAgendada, status) => {

    const currentDate = new Date();
    const scheduleDate = new Date(dataAgendada);

    const statusKey = ((agendado === 1) && (currentDate < scheduleDate)) ? 9 : status

    const statusString = {
        1: {
            label: 'Pendente',
            classes: {
                bg: 'bg-warning',
                text: 'text-warning',
                icon: 'ri-error-warning-fill'
            }
        },
        2: {
            label: 'Cancelado pelo Usuário',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        3: {
            label: 'Não Localizado',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        4: {
            label: 'Trote',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        5: {
            label: 'Recusado',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        6: {
            label: 'Despachado',
            classes: {
                bg: 'bg-dispatched',
                text: 'text-dispatched',
                icon: 'ri-e-bike-2-fill'
            }
        },
        7: {
            label: 'Entregue',
            classes: {
                bg: 'bg-info',
                text: 'text-info',
                icon: 'ri-check-double-fill'
            }
        },
        8: {
            label: 'Aceito',
            classes: {
                bg: 'bg-accepted',
                text: 'text-accepted',
                icon: 'ri-check-double-fill'
            }
        },
        9: {
            label: 'Agendado',
            classes: {
                bg: 'bg-muted',
                text: 'text-muted',
                icon: 'ri-calendar-schedule-fill'
            }
        }
    }
    return statusString[statusKey]
}
