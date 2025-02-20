import { DateUtil } from '@/util'
export default {
    isValidDateFormat: (dateString) => {
        const isoRegex = /^\d{4}-\d{2}-\d{2}$/;
        const brRegex = /^\d{2}\/\d{2}\/\d{4}$/;
        if (isoRegex.test(dateString)) return { isValid: true, format: "YYYY-MM-DD" };
        if (brRegex.test(dateString)) return { isValid: true, format: "DD/MM/YYYY" };
        return { isValid: false, format: null };
    },

    getDateComponents: (dateString) => {
        const { isValid, format } = DateUtil.isValidDateFormat(dateString);
        if (!isValid) return null;
        return format === "YYYY-MM-DD"
            ? (([YYYY, MM, DD]) => ({ DD, MM, YYYY }))(dateString.split("-"))
            : (([DD, MM, YYYY]) => ({ DD, MM, YYYY }))(dateString.split("/"));
    },

    // Converte data para ISO. Entrada esperada: "DD/MM/YYYY HH:mm"
    dateToISOFormat: (dateTimeString) => {
        const parts = dateTimeString.split(" ");
        if (parts.length < 2) return null; // Tratamento de casos extremos
        const [date, time] = parts;
        const components = DateUtil.getDateComponents(date);
        if (!components) return null;
        const { DD, MM, YYYY } = components;
        const timeParts = time.split(":");
        if (timeParts.length < 2) return null;
        const [HH, mm] = timeParts;
        return new Date(`${YYYY}-${MM}-${DD}T${HH}:${mm}`);
    },

    dateToDayMonthYearFormat: (rawDate) => {
        if (!rawDate) return rawDate;
        try {
            const date = new Date(rawDate);
            const YYYY = date.getFullYear();
            const unformattedMonth = date.getMonth() + 1;
            const unformattedDay = date.getDate();
            const unformattedHour = date.getHours();
            const unformattedMinutes = date.getMinutes();

            const dd = unformattedDay < 10 ? `0${unformattedDay}` : unformattedDay;
            const MM = unformattedMonth < 10 ? `0${unformattedMonth}` : unformattedMonth;
            const hh = unformattedHour < 10 ? `0${unformattedHour}` : unformattedHour;
            const mm = unformattedMinutes < 10 ? `0${unformattedMinutes}` : unformattedMinutes;

            const extenseDate = DateUtil.checkDate(`${dd}/${MM}/${YYYY} ${hh}:${mm}`);
            return { date: `${dd}/${MM}/${YYYY}`, time: `${hh}:${mm}`, dateTime: `${extenseDate} às ${hh}:${mm}` };
        } catch (err) {
            console.error(err);
            return rawDate;
        }
    },

    checkDate: (dateStr) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);

        const checkDateObj = DateUtil.dateToISOFormat(dateStr);
        if (!checkDateObj) return dateStr.split(" ")[0];
        checkDateObj.setHours(0, 0, 0, 0);

        if (checkDateObj.getTime() === today.getTime()) return "Hoje";
        if (checkDateObj.getTime() === tomorrow.getTime()) return "Amanhã";
        if (checkDateObj.getTime() === yesterday.getTime()) return "Ontem";
        return dateStr.split(" ")[0];
    },
};
