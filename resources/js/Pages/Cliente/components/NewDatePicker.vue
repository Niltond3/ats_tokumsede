<script setup>
import { ref } from 'vue';
import {
    getLocalTimeZone,
    today,
} from '@internationalized/date';
import {
    DatePickerArrow,
    DatePickerCalendar,
    DatePickerCell,
    DatePickerCellTrigger,
    DatePickerContent,
    DatePickerField,
    DatePickerGrid,
    DatePickerGridBody,
    DatePickerGridHead,
    DatePickerGridRow,
    DatePickerHeadCell,
    DatePickerHeader,
    DatePickerHeading,
    DatePickerInput,
    DatePickerNext,
    DatePickerPrev,
    DatePickerRoot,
    DatePickerTrigger,
} from 'radix-vue';
import Button from '@/components/Button.vue';
import { useDateFormatter } from 'radix-vue';
import { createDecade, createYear, toDate } from 'radix-vue/date';
import { createReusableTemplate, useToggle, useVModel } from '@vueuse/core';
import { RiCalendarLine as CalendarIcon } from "vue-remix-icons";
import { RiArrowLeftWideLine as ArrowLeftIcon } from "vue-remix-icons";
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";
import { FormItem, FormMessage, FormLabel, FormControl } from '@/components/ui/form/';


const formatter = useDateFormatter('pt-br');

const [DefineMonthTemplate, ReuseMonthTemplate] = createReusableTemplate();
const [DefineDecadesTemplate, ReuseDecadeTemplate] = createReusableTemplate();
const [DefineGridTemplate, ReuseGridTemplate] = createReusableTemplate();

const [isGridView, toggleGridView] = useToggle(true);
const [isMonthView, toggleMonthView] = useToggle(false);
const [isDecadeView, toggleDecadesView] = useToggle(false);


const props = withDefaults(defineProps(), {
    modelValue: undefined,
    placeholder() {
        return today(getLocalTimeZone())
    },
    weekdayFormat: 'short',
})


const placeholder = ref(today(getLocalTimeZone()));


// const placeholder = useVModel(props, 'modelValue', {
//     passive: true,
//     defaultValue: today(getLocalTimeZone()),
// })


</script>

<template>
    <FormItem>
        <DefineMonthTemplate v-slot="{ date }">
            <div class="grid grid-cols-3">
                <button :class="['p-2', placeholder.month === month.month && 'bg-green-300']"
                    v-for="month in createYear({ dateObj: date })" :key="month.toString()" @click="() => {
                        placeholder = placeholder.set({
                            month: month.month,
                        });
                        toggleGridView(true);
                        toggleMonthView(false);
                    }
                        ">
                    {{ formatter.custom(toDate(month), { month: 'short' }) }}
                </button>
            </div>
        </DefineMonthTemplate>

        <DefineDecadesTemplate v-slot="{ date }">
            <div class="grid grid-cols-2 gap-4">
                <button :class="[
                    'block w-full text-center',
                    placeholder.year === yearValue.year && 'bg-red-600',
                ]" v-for="yearValue in createDecade({
                    dateObj: date,
                    startIndex: -5,
                    endIndex: 5,
                })" :key="yearValue.toString()" @click="() => {
                    placeholder = placeholder.set({
                        year: yearValue.year,
                    });

                    toggleDecadesView(false);
                    toggleMonthView(true);
                }
                    ">
                    {{ yearValue.year }}
                </button>
            </div>
        </DefineDecadesTemplate>

        <DefineGridTemplate v-slot="{ weekDays, grid, date }">
            <DatePickerGrid v-for="month in grid" :key="month.value.toString()"
                class="w-full border-collapse select-none space-y-1">
                <DatePickerGridHead>
                    <DatePickerGridRow class="mb-1 flex w-full justify-between">
                        <DatePickerHeadCell v-for="day in weekDays" :key="day"
                            class="w-8 rounded-md text-xs text-green8">
                            {{ day }}
                        </DatePickerHeadCell>
                    </DatePickerGridRow>
                </DatePickerGridHead>
                <DatePickerGridBody>
                    <DatePickerGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`"
                        class="flex w-full">
                        <DatePickerCell v-for="weekDate in weekDates" :key="weekDate.toString()" :date="weekDate">
                            <DatePickerCellTrigger :day="weekDate" :month="month.value"
                                class="relative flex items-center justify-center whitespace-nowrap rounded-[9px] border border-transparent bg-transparent text-sm font-normal text-black w-8 h-8 outline-none focus:shadow-[0_0_0_2px] focus:shadow-black hover:border-black data-[selected]:bg-black data-[selected]:font-medium data-[disabled]:text-black/30 data-[selected]:text-white data-[unavailable]:pointer-events-none data-[unavailable]:text-black/30 data-[unavailable]:line-through before:absolute before:top-[5px] before:hidden before:rounded-full before:w-1 before:h-1 before:bg-white data-[today]:before:block data-[today]:before:bg-green9 data-[selected]:before:bg-white" />
                        </DatePickerCell>
                    </DatePickerGridRow>
                </DatePickerGridBody>
            </DatePickerGrid>
        </DefineGridTemplate>

        <div class="flex flex-col gap-2">
            <FormLabel for="date-field">Birthday</FormLabel>
            <DatePickerRoot id="date-field" v-model:placeholder="placeholder">
                <DatePickerField v-slot="{ segments }"
                    class="flex select-none bg-white items-center justify-between rounded-lg text-center text-green10 border border-transparent p-1 w-40 data-[invalid]:border-red-500">
                    <div class="flex items-center">
                        <FormControl>
                            <template v-for="item in segments" :key="item.part">
                                <FormItem>
                                    <FormControl>
                                        <DatePickerInput v-if="item.part === 'literal'" :part="item.part"
                                            value="{{item.value}}">
                                            {{ item.value }}
                                        </DatePickerInput>
                                        <DatePickerInput v-else :part="item.part"
                                            class="rounded-md p-0.5 focus:outline-none focus:shadow-[0_0_0_2px] focus:shadow-black data-[placeholder]:text-green9"
                                            value='{{item.value}}'>
                                            {{ item.value }}
                                        </DatePickerInput>
                                    </FormControl>
                                </FormItem>
                            </template>
                        </FormControl>
                    </div>

                    <DatePickerTrigger class="focus:shadow-[0_0_0_2px] rounded-md text-xl p-1 focus:shadow-black">
                        <FormControl>
                            <Button class="icon" variant="icon">
                                <CalendarIcon />
                            </Button>
                        </FormControl>
                    </DatePickerTrigger>
                    <FormMessage />
                </DatePickerField>

                <DatePickerContent :side-offset="4"
                    class="rounded-xl bg-white shadow-[0_10px_38px_-10px_hsla(206,22%,7%,.35),0_10px_20px_-15px_hsla(206,22%,7%,.2)] focus:shadow-[0_10px_38px_-10px_hsla(206,22%,7%,.35),0_10px_20px_-15px_hsla(206,22%,7%,.2),0_0_0_2px_theme(colors.green7)] will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                    <DatePickerArrow class="fill-white" />
                    <DatePickerCalendar v-slot="{ weekDays, grid, date }" class="p-4">
                        <DatePickerHeader class="flex items-center justify-between">
                            <DatePickerPrev
                                class="inline-flex items-center cursor-pointer text-black justify-center rounded-[9px] bg-transparent w-8 h-8 hover:bg-black hover:text-white active:scale-98 active:transition-all focus:shadow-[0_0_0_2px] focus:shadow-black"
                                :prev-page="(date) => {
                                    if (isMonthView || isDecadeView) {
                                        return date.subtract({ years: 1 })
                                    }
                                    return date.subtract({ months: 1 })
                                }">
                                <ArrowLeftIcon class="icon w-6 h-6" />
                            </DatePickerPrev>

                            <DatePickerHeading class="text-black font-medium" v-slot="ctx">
                                <template v-if="isGridView">
                                    <div class="flex gap-4">
                                        <button @click="() => {
                                            toggleGridView(false);
                                            toggleMonthView(true);
                                        }
                                            ">
                                            {{
                                                formatter.custom(toDate(placeholder), { month: 'long' })
                                            }}
                                        </button>
                                        <button @click="() => {
                                            toggleGridView(false);
                                            toggleDecadesView(true);
                                        }
                                            ">
                                            {{ placeholder.year }}
                                        </button>
                                    </div>
                                </template>
                                <template v-if="isMonthView">
                                    <button @click="() => {
                                        toggleMonthView(false);
                                        toggleDecadesView(true);
                                    }
                                        ">
                                        {{ placeholder.year }}
                                    </button>
                                </template>
                                <template v-if="isDecadeView">
                                    <div>
                                        {{ placeholder.year }}
                                        <div class="text-xs">
                                            show decade with some util which im not yet aware
                                        </div>
                                    </div>
                                </template>
                            </DatePickerHeading>
                            <DatePickerNext
                                class="inline-flex items-center cursor-pointer text-black justify-center rounded-[9px] bg-transparent w-8 h-8 hover:bg-black hover:text-white active:scale-98 active:transition-all focus:shadow-[0_0_0_2px] focus:shadow-black"
                                :next-page="(date) => {
                                    if (isMonthView || isDecadeView) {
                                        return date.add({ years: 1 })
                                    }
                                    return date.add({ months: 1 })
                                }">
                                <ArrowRightIcon class="icon w-6 h-6" />
                            </DatePickerNext>
                        </DatePickerHeader>
                        <div class="flex flex-col space-y-4 pt-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                            <template v-if="isGridView">
                                <ReuseGridTemplate :weekDays :grid :date />
                            </template>
                            <template v-else-if="isMonthView">
                                <ReuseMonthTemplate :date />
                            </template>
                            <template v-else-if="isDecadeView">
                                <ReuseDecadeTemplate :date />
                            </template>
                        </div>
                    </DatePickerCalendar>
                </DatePickerContent>
            </DatePickerRoot>
        </div>
    </FormItem>
</template>
