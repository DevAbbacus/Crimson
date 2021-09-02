<template>
    <app-layout :status="3">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-profile-information-form
                            :name="$page.user.name"
                            :email="$page.user.email" />

                <jet-section-border />

                <current-rms-information-form
                            :api_token="$page.user.api_token"
                            :sub_domain="$page.user.sub_domain"
                            :site_url="$page.user.site_url"
                            :user_type="$page.user.user_type" />

                <jet-section-border />

                <update-password-form class="mt-10 sm:mt-0" />

                <jet-section-border />

                <logout-other-browser-sessions-form :sessions="sessions" class="mt-10 sm:mt-0" />

                <jet-section-border />

                <delete-user-form class="mt-10 sm:mt-0" />
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import DeleteUserForm from './DeleteUserForm'
    import JetSectionBorder from './../../Jetstream/SectionBorder'
    import LogoutOtherBrowserSessionsForm from './LogoutOtherBrowserSessionsForm'
    import UpdatePasswordForm from './UpdatePasswordForm'
    import UpdateProfileInformationForm from './UpdateProfileInformationForm'
    import CurrentRmsInformationForm from './CurrentRmsInformationForm'

    export default {
        props: ['sessions'],

        components: {
            AppLayout,
            DeleteUserForm,
            JetSectionBorder,
            LogoutOtherBrowserSessionsForm,
            UpdatePasswordForm,
            UpdateProfileInformationForm,
            CurrentRmsInformationForm
        },
    }
</script>
