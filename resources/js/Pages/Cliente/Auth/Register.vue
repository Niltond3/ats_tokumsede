<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/components/Button.vue';
import { Head, Link } from '@inertiajs/vue3';
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";
import { RiCalendarLine as CalendarIcon } from "vue-remix-icons";
import { format } from 'date-fns'
import validator from 'validator'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { vAutoAnimate } from '@formkit/auto-animate/vue'
import { cn } from "@/lib/utils";
import axios from 'axios';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Input } from '@/components/ui/input'
import { Calendar } from '@/components/ui/calendar'


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const formSchema = toTypedSchema(z.object({
    nome: z.string({ required_error: 'Informar seu nome é obrigatório' }),
    telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    senha: z.string({ required_error: 'Senha obrigatória' }).min(4),
    confirmSenha: z.string({ required_error: 'Confirme sua senha' }).min(4),
    sexo: z.enum(['0', '1', '2']),
    dataNascimento: z.date(),
    email: z.string({ required_error: "e-mail obrigatório" }),
    tipoPessoa: z.enum(),
}).superRefine(({ confirmSenha, senha }, ctx) => {
    if (confirmSenha !== senha) {
        ctx.addIssue({
            code: "custom",
            message: "As senhas devem ser iguais",
            path: ['confirmSenha']
        });
    }
}));

const { handleSubmit, isSubmitting, values } = useForm({
    validationSchema: formSchema,
    initialValues: {
        remember: false
    }
})


const onSubmit = handleSubmit((values, { resetField }) => {
    const phoneRaw = values.telefone.replace(/\D/g, '')

    const ddd = phoneRaw.slice(0, 2);
    const telefone = phoneRaw.slice(2);

    const payload = {
        ddd,
        telefone,
        senha: values.senha,
        sexo: values.sexo,
        dataNascimento: values.dataNascimento,
        email: values.email,
        tipoPessoa: values.tipoPessoa
    }
    console.log(payload)

    // axios.post(route('cliente.register'), payload)
    //     .then((response) => {
    //         console.log('response')
    //         console.log(response)
    //         resetField('senha')
    //         location.reload();
    //     }).catch((error) => {
    //         console.error(error);
    //         resetField('senha')
    //     })

})

</script>

<template>
    <GuestLayout>

        <Head title="Registre-se" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form class="space-y-6" @submit="onSubmit">
            <FormField v-slot="{ componentField }" name="nome">
                <FormItem v-auto-animate>
                    <FormLabel>Nome</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel" placeholder="Nome completo"
                            v-bind="componentField" autocomplete="name" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="telefone">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="senha">
                <FormItem v-auto-animate>
                    <FormLabel>Senha</FormLabel>
                    <FormControl>
                        <Input type="password" placeholder="Senha" v-bind="componentField"
                            autocomplete="new-password" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="confirmSenha">
                <FormItem v-auto-animate>
                    <FormLabel>Confirmação de senha</FormLabel>
                    <FormControl>
                        <Input type="password" placeholder="Confirme sua senha" v-bind="componentField"
                            autocomplete="new-password" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" type="radio" name="sexo">
                <FormItem v-auto-animate>
                    <FormLabel>selecione seu sexo</FormLabel>

                    <FormControl>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">
                                    {{ values.sexo }}
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-80">
                                <div class="grid gap-4">
                                    <div class="grid gap-2">
                                        <RadioGroup default-value="0" v-bind="componentField">
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="r1" value="0" />
                                                <Label for="r1">Não Informar</Label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="r2" value="1" />
                                                <Label for="r2">Másculino</Label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="r3" value="2" />
                                                <Label for="r3">Feminino</Label>
                                            </div>
                                        </RadioGroup>
                                    </div>
                                </div>
                            </PopoverContent>
                        </Popover>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField, value }" name="dataNascimento">
                <FormItem class="flex flex-col">
                    <FormLabel>Data de Nascimento</FormLabel>
                    <Popover>
                        <PopoverTrigger as-child>
                            <FormControl>
                                <Button variant="outline" :class="cn(
                                    'w-[240px] ps-3 text-start font-normal',
                                    !value && 'text-muted-foreground',
                                )">
                                    <span>{{ value ? format(value, "PPP") : "Pick a date" }}</span>
                                    <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
                                </Button>
                            </FormControl>
                        </PopoverTrigger>
                        <PopoverContent class="p-0">
                            <Calendar v-bind="componentField" />
                        </PopoverContent>
                    </Popover>
                    <FormDescription>
                        Sua data de Nascimento é usada para calcular sua idade
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="email">
                <FormItem v-auto-animate>
                    <FormLabel>E-mail</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="email" placeholder="E-mail válido"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="tipoPessoa">
                <FormItem v-auto-animate>
                    <FormLabel>CPF/CNPJ</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="text"
                            v-mask="['###.###.###-##', '##.###.###/####-##']" placeholder="Número de telefone"
                            v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <div class="flex gap-4p">
                <Button type="submit" :class="{ 'opacity-25': isSubmitting }" :disabled="isSubmitting">
                    <i class="icon text-xl">
                        <LoginIcon />
                    </i>
                    Cadastrar
                </Button>

            </div>
        </form>
    </GuestLayout>
</template>
