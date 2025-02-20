export default {
    getError: (error) => {
        let e = error;
        if (error.response) {
            e = error.response.data;
            // Usa optional chaining para simplificar
            if (error.response.data?.error) e = error.response.data.error;
            if (error.response.data?.message) e = error.response.data.message;
        } else if (error.message) {
            e = error.message;
        } else {
            e = "Ocorreu um erro inesperado";
        }
        if (e === "Unauthenticated") {
            console.error(e);
            window.location.reload();
        }
        return e;
    },
};
