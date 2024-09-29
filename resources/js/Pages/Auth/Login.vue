<script setup>
import Checkbox from '@/components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Button from '@/components/Button.vue';
import TextInput from '@/components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    login: '',
    senha: '',
    remember: false,
});




const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('senha'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="login" value="Login" />

                <TextInput id="login" type="login" class="mt-1 block w-full" v-model="form.login" required autofocus
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <div class="mt-4">
                <InputLabel for="senha" value="Senha" />

                <TextInput id="senha" type="password" class="mt-1 block w-full" v-model="form.senha" required
                    autocomplete="current-password" />

                <InputError class="mt-2" :message="form.errors.senha" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>
            <div class="flex items-center justify-end mt-4">
                <Button class="ms-4 btn--primary" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    <i class="icon text-xl">
                        <LoginIcon />
                    </i>
                    <span class="hidden min-[768px]:block">Log in</span>
                </Button>


                <Button href="#informations" class="ms-4" :disabled="form.processing">
                    <span class="hidden min-[768px]:block text-zinc-700">Registrar-se</span>
                    <i class="icon icon--chevron-right text-xl hidden min-[425px]:block">
                        <ArrowRightIcon />
                    </i>
                </Button>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Esqueceu sua senha?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
