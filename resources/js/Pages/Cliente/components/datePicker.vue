<script setup>
import { computed } from 'vue'
import { CalendarRoot, useDateFormatter, useForwardPropsEmits } from 'radix-vue'
import { createDecade, createYear, toDate } from 'radix-vue/date'
import { getLocalTimeZone, today } from '@internationalized/date'
import { useVModel } from '@vueuse/core'
import {
    CalendarCell,
    CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell,
    CalendarHeader,
    CalendarHeading
} from '@/components/ui/calendar'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { cn } from "@/lib/utils";
import { FormItem, FormMessage, FormLabel, FormControl } from '@/components/ui/form/';


const props = defineProps({
    modelValue: { type: null, required: false },
    multiple: { type: Boolean, required: false },
    defaultValue: { type: null, required: false },
    defaultPlaceholder: { type: null, required: false },
    placeholder: { type: null, required: false },
    pagedNavigation: { type: Boolean, required: false },
    preventDeselect: { type: Boolean, required: false },
    weekStartsOn: { type: Number, required: false },
    weekdayFormat: { type: String, required: false },
    calendarLabel: { type: String, required: false },
    fixedWeeks: { type: Boolean, required: false },
    maxValue: { type: null, required: false },
    minValue: { type: null, required: false },
    locale: { type: String, required: false },
    numberOfMonths: { type: Number, required: false },
    disabled: { type: Boolean, required: false },
    readonly: { type: Boolean, required: false },
    initialFocus: { type: Boolean, required: false },
    isDateDisabled: { type: Function, required: false },
    isDateUnavailable: { type: Function, required: false },
    dir: { type: String, required: false },
    nextPage: { type: Function, required: false },
    prevPage: { type: Function, required: false },
    asChild: { type: Boolean, required: false },
    as: { type: null, required: false },
    class: { type: null, required: false },
});


const emits = defineEmits(["update:modelValue", "update:placeholder"]);

const delegatedProps = computed(() => {
    const { class: _, placeholder: __, ...delegated } = props

    return delegated
})


const placeholder = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: today(getLocalTimeZone()),
})

const forwarded = useForwardPropsEmits(delegatedProps, emits);

const formatter = useDateFormatter('pt-br')
</script>

<template>
    <CalendarRoot locale="pt-br" v-slot="{ date, grid, weekDays }" :class="cn('rounded-md border p-3', props.class)"
        :v-model:placeholder="placeholder" v-bind="forwarded">
        <CalendarHeader>
            <CalendarHeading class="flex w-full items-center justify-between gap-2">
                <FormItem>
                    <Select :default-value="placeholder.month.toString()" @update:model-value="(v) => {
                    if (!v || !placeholder) return;
                    if (Number(v) === placeholder?.month) return;
                    placeholder = placeholder.set({
                        month: Number(v),
                    })
                }">
                    <FormControl>
                        <SelectTrigger aria-label="Select month" class="w-[60%]">
                        <SelectValue placeholder="Select month" />
                    </SelectTrigger>
                    </FormControl>
                    <SelectContent class="max-h-[200px]">
                        <SelectItem v-for="month in createYear({ dateObj: date })" :key="month.toString()"
                            :value="month.month.toString()">
                            {{ formatter.custom(toDate(month), { month: 'long' }) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                </FormItem>

                <FormItem>
                    <Select :default-value="placeholder.year.toString()" @update:model-value="(v) => {
                    if (!v || !placeholder) return;
                    if (Number(v) === placeholder?.year) return;
                    placeholder = placeholder.set({
                        year: Number(v),
                    })
                }">
                    <FormControl>
                        <SelectTrigger aria-label="Select year" class="w-[40%]">
                        <SelectValue placeholder="Select year" />
                    </SelectTrigger>
                    </FormControl>
                    <SelectContent class="max-h-[200px]">
                        <SelectItem v-for="yearValue in createDecade({ dateObj: date, startIndex: -100, endIndex: 1 })"
                            :key="yearValue.toString()" :value="yearValue.year.toString()">
                            {{ yearValue.year }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                </FormItem>

            </CalendarHeading>
        </CalendarHeader>

        <div class="flex flex-col space-y-4 pt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
            <CalendarGrid v-for="month in grid" :key="month.value.toString()">
                <CalendarGridHead>
                    <CalendarGridRow>
                        <CalendarHeadCell v-for="day in weekDays" :key="day">
                            {{ day }}
                        </CalendarHeadCell>
                    </CalendarGridRow>
                </CalendarGridHead>
                <CalendarGridBody>
                    <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`"
                        class="mt-2 w-full">
                        <CalendarCell v-for="weekDate in weekDates" :key="weekDate.toString()" :date="weekDate">
                            <CalendarCellTrigger :day="weekDate" :month="month.value" />
                        </CalendarCell>
                    </CalendarGridRow>
                </CalendarGridBody>
            </CalendarGrid>
        </div>
    </CalendarRoot>
</template>
