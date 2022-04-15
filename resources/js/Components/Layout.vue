<template>
    <div>
        <sidebar />
        <div class="relative md:ml-64 bg-blueGray-100">
            <admin-navbar />
            <header-stats />

            <div class="flex-1 overflow-hidden px-4 py-8 md:p-4 overflow-y-auto" scroll-region>
                <slot />
                
            </div>
        </div>
        
    </div>
</template>
<script>

import AdminNavbar from "@/Components/Navbars/AdminNavbar.vue";
import Sidebar from "@/Components/Sidebar/Sidebar.vue";
import HeaderStats from "@/Components/Headers/HeaderStats.vue";
import FooterAdmin from "@/Components/Footers/FooterAdmin.vue";



export default {
    name: "admin-layout",
    props: {
        chanels: Array,
        errors: Object
    },
    components: {
        AdminNavbar,
        Sidebar,
        HeaderStats,
        FooterAdmin,
    

    },
};
</script>
<style scoped>
.section,
section {
    margin-left: auto;
    margin-right: auto;
    max-width: 1700px;
    padding: 20px;
    position: relative;
}
</style>