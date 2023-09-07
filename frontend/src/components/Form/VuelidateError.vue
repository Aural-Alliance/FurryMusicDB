<template>
    <div
        v-for="message in errorMessages"
        :key="message"
        class="invalid-feedback"
    >
        {{ message }}
    </div>
</template>

<script setup lang="ts">
import {get, map} from "lodash";
import {computed} from "vue";

const props = defineProps({
    field: {
        type: Object,
        required: true
    }
});

const messages = {
    required: () => 'This field is required.',
    minLength: (params) => `This field must have at least ${params.min} letters.`,
    maxLength: (params) => `This field must have at most ${params.max} letters.`,
    between: (params) => `This field must be between ${params.min} and ${params.max}.`,
    alpha: () => 'This field must only contain alphabetic characters.',
    alphaNum: () => 'This field must only contain alphanumeric characters.',
    numeric: () => 'This field must only contain numeric characters.',
    integer: () => 'This field must be a valid integer.',
    decimal: () => 'This field must be a valid decimal number.',
    email: () => 'This field must be a valid e-mail address.',
    ipAddress: () => 'This field must be a valid IP address.',
    url: () => 'This field must be a valid URL.',
    validatePassword: () => 'This password is too common or insecure.'
};

const errorMessages = computed(() => {
    return map(
        props.field.$errors,
        (error) => {
            const message = get(messages, error.$validator, null);
            if (null !== message) {
                return message(error.$params);
            } else {
                return error.$message;
            }
        }
    );
});
</script>
