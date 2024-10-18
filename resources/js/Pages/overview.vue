<script setup>
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const page = usePage()
const getOverview = () => {
    console.log(page)

    const client = page.props.auth.user

    console.log(client)
    // axios.get(`produtos/${client.enderecos[0].id}`)


    axios.get(`produtos/${client.enderecos[0].id}`)
        .then((response) => {
            console.log('response')
            console.log(response.data)
        }).catch((error) => {
            console.error(error);
        })
}

getOverview()
</script>

<template>
    <div class="container p-0">
        
    </div>
</template>
