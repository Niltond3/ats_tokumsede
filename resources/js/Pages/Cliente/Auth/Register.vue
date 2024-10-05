<script setup>
import { computed, h, ref } from 'vue';
import { CalendarDate, DateFormatter, getLocalTimeZone, parseDate } from '@internationalized/date';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/components/Button.vue';
import { Head } from '@inertiajs/vue3';
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { RiCalendarLine as CalendarIcon } from "vue-remix-icons";
import { format, parseISO } from 'date-fns';
import ptBR from 'date-fns/locale/pt-BR';
import validator from 'validator'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { vAutoAnimate } from '@formkit/auto-animate/vue'
import { cn } from "@/lib/utils";
import axios from 'axios';
import {
    FormControl,
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
import DatePicker from '../components/datePicker/datePicker.vue'


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const getSexo = {
    0: 'Não Informado',
    1: 'Masculino',
    2: 'Feminino',
}

const getTipoPessoa = {
    1: 'Pessoa Física',
    2: 'Pessoa Jurídica',
}
const formSchema = toTypedSchema(z.object({
    nome: z.string({ required_error: 'Informar seu nome é obrigatório' }),
    telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    senha: z.string({ required_error: 'Senha obrigatória' }).min(4),
    confirmSenha: z.string({ required_error: 'Confirme sua senha' }).min(4),
    sexo: z.enum(['0', '1', '2']).default('0'),
    // dataNascimento: z.object(),
    email: z.string({ required_error: "e-mail obrigatório" }),
    tipoPessoa: z.string(),
}).superRefine(({ confirmSenha, senha }, ctx) => {
    if (confirmSenha !== senha) {
        ctx.addIssue({
            code: "custom",
            message: "As senhas devem ser iguais",
            path: ['confirmSenha']
        });
    }
}));

const { handleSubmit, isSubmitting, values, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: {
        sexo: '0'
    }
})

const getDataFormat = (data) => {
    console.log(data)
    console.log(values)
    return format(parseISO(data.toString()), "dd'º de' MMM',' yyyy", { locale: ptBR })
}

const onSubmit = handleSubmit((values, { resetField }) => {
    const phoneRaw = values.telefone.replace(/\D/g, '')

    const ddd = phoneRaw.slice(0, 2);
    const telefone = phoneRaw.slice(2);

    const payload = {
        ddd,
        telefone,
        senha: values.senha,
        sexo: values.sexo,
        // dataNascimento: values.dataNascimento,
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
            <FormField v-slot="{ componentField }" name="sexo">
                <FormItem v-auto-animate class="flex flex-col">
                    <FormLabel>selecione seu sexo</FormLabel>
                    <Popover>
                        <PopoverTrigger as-child>
                            <FormControl>
                                <Button variant="outline"
                                    class="text-slate-500 text-sm !p-2 min-h-[22px] !rounded-sm font-normal flex justify-start !px-3">
                                    {{ getSexo[values.sexo] }}
                                </Button>
                            </FormControl>
                        </PopoverTrigger>
                        <PopoverContent class="w-80">
                            <FormControl>
                                <RadioGroup default-value="0" v-bind="componentField">
                                    <FormItem class="flex items-center space-x-2">
                                        <FormControl>
                                            <RadioGroupItem value="0" />
                                        </FormControl>
                                        <FormLabel>Não Informado</FormLabel>
                                    </FormItem>
                                    <FormItem class="flex items-center space-x-2">
                                        <FormControl>
                                            <RadioGroupItem value="1" />
                                        </FormControl>
                                        <FormLabel>Másculino</FormLabel>
                                    </FormItem>
                                    <FormItem class="flex items-center space-x-2">
                                        <FormControl>
                                            <RadioGroupItem value="2" />
                                        </FormControl>
                                        <FormLabel>Feminino</FormLabel>
                                    </FormItem>
                                </RadioGroup>
                            </FormControl>
                        </PopoverContent>
                    </Popover>
                    <FormMessage />
                </FormItem>
            </FormField>
            <!-- <FormField v-slot="{ componentField, value, setValue }" name="dataNascimento">
                <FormItem class="flex flex-col">
                    <FormLabel>Data de Nascimento</FormLabel>
                    <Popover>
                        <PopoverTrigger as-child>
                            <FormControl>
                                <Button variant="outline" :class="cn(
                                    'text-slate-500 text-sm !p-2 min-h-[22px] !rounded-sm font-normal flex justify-start !px-3',
                                    !value && 'text-muted-foreground',
                                )">
                                    <span>{{ values.dataNascimento ? getDataFormat(values.dataNascimento) : `Selecione
                                        uma data` }}
                                    </span>
                                    <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
                                </Button>
                            </FormControl>
                        </PopoverTrigger>
                        <PopoverContent class="p-0">
                            <DatePicker v-bind="componentField" @update:model-value="(v) => {
                                console.log(v)
                            }" />
                        </PopoverContent>
                    </Popover>
                    <FormMessage />
                </FormItem>
            </FormField> -->
            <FormField v-slot="{ componentField }" name="email">
                <FormItem v-auto-animate>
                    <FormLabel>E-mail</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="email" placeholder="E-mail válido"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
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
                <Button type="submit" class="icon text-xl" :class="{ 'opacity-25': isSubmitting }"
                    :disabled="isSubmitting">
                    <LoginIcon />
                    Cadastrar
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
