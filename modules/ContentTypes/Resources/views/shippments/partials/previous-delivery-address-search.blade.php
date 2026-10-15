<style>
    .previous-delivery-address-search {
        background: linear-gradient(135deg, rgba(101, 118, 255, 0.07), rgba(255, 255, 255, 0.98));
        border: 1px solid rgba(101, 118, 255, 0.2);
        border-radius: 6px;
        padding: 1rem;
    }
    .previous-delivery-address-search__icon {
        align-items: center;
        background: rgba(101, 118, 255, 0.13);
        border-radius: 50%;
        color: #6576ff;
        display: inline-flex;
        flex: 0 0 38px;
        font-size: 18px;
        height: 38px;
        justify-content: center;
        margin-inline-end: 0.75rem;
        width: 38px;
    }
    .previous-delivery-address-search__field {
        position: relative;
    }
    .previous-delivery-address-search__field > .icon {
        color: #8094ae;
        inset-inline-start: 0.9rem;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
    }
    .previous-delivery-address-search__field .form-control {
        padding-inline-start: 2.5rem;
        padding-inline-end: 2.5rem;
    }
    .previous-delivery-address-search__spinner {
        inset-inline-end: 0.9rem;
        position: absolute;
        top: calc(50% - 0.5rem);
    }
    .previous-delivery-address-search__results {
        background: #fff;
        border: 1px solid #dbdfea;
        border-radius: 5px;
        box-shadow: 0 8px 24px rgba(31, 43, 58, 0.13);
        inset-inline: 0;
        margin-top: 0.35rem;
        max-height: 320px;
        overflow-y: auto;
        position: absolute;
        top: 100%;
        z-index: 1050;
    }
    .previous-delivery-address-search__result {
        background: transparent;
        border: 0;
        border-bottom: 1px solid #edf2f9;
        color: #364a63;
        cursor: pointer;
        display: block;
        padding: 0.8rem 1rem;
        text-align: start;
        width: 100%;
    }
    .previous-delivery-address-search__result:last-child {
        border-bottom: 0;
    }
    .previous-delivery-address-search__result:hover,
    .previous-delivery-address-search__result:focus {
        background: #f5f6fa;
        outline: 0;
    }
    .previous-delivery-address-search__result-title {
        align-items: flex-start;
        display: flex;
        font-weight: 600;
        gap: 0.45rem;
    }
    .previous-delivery-address-search__result-meta {
        color: #8094ae;
        display: flex;
        flex-wrap: wrap;
        font-size: 0.78rem;
        gap: 0.3rem 1rem;
        margin-top: 0.4rem;
        padding-inline-start: 1.45rem;
    }
    .previous-delivery-address-search__result-meta span {
        align-items: center;
        display: inline-flex;
        gap: 0.35rem;
        overflow-wrap: anywhere;
    }
    .previous-delivery-address-search__message {
        color: #8094ae;
        padding: 1rem;
        text-align: center;
    }
</style>

<script type="text/x-template" id="previous-delivery-address-search-template">
    <div class="previous-delivery-address-search">
        <div class="d-flex align-items-center mb-3">
            <span class="previous-delivery-address-search__icon">
                <em class="icon ni ni-map-pin-fill"></em>
            </span>
            <div>
                <label class="form-label mb-1">{{ __('contenttypes::strings.shippments.previous_delivery_addresses') }}</label>
                <span class="form-note">{{ __('contenttypes::strings.shippments.previous_delivery_address_hint') }}</span>
            </div>
        </div>
        <div class="previous-delivery-address-search__field">
            <em class="icon ni ni-search"></em>
            <input
                type="search"
                class="form-control"
                autocomplete="off"
                :disabled="!cityId"
                v-model.trim="query"
                placeholder="{{ __('contenttypes::strings.shippments.previous_delivery_address_search') }}"
                @focus="open"
                @input="queueSearch"
                @keydown.esc="opened = false"
                @blur="closeLater">
            <span v-if="loading" class="spinner-border spinner-border-sm previous-delivery-address-search__spinner" role="status"></span>
            <div v-if="opened" class="previous-delivery-address-search__results">
                <button
                    v-for="result in results"
                    :key="result.source_id"
                    type="button"
                    class="previous-delivery-address-search__result"
                    @mousedown.prevent="select(result)">
                    <span class="previous-delivery-address-search__result-title">
                        <em class="icon ni ni-map-pin"></em>
                        <span v-text="result.full_address || result.primary_contact_company_name"></span>
                    </span>
                    <span class="previous-delivery-address-search__result-meta">
                        <span v-if="result.primary_contact_company_name">
                            <em class="icon ni ni-building"></em>
                            <span v-text="result.primary_contact_company_name"></span>
                        </span>
                        <span v-if="result.primary_contact_name">
                            <em class="icon ni ni-user"></em>
                            <span v-text="result.primary_contact_name"></span>
                        </span>
                        <span v-if="result.primary_contact_email" dir="ltr">
                            <em class="icon ni ni-mail"></em>
                            <span v-text="result.primary_contact_email"></span>
                        </span>
                        <span v-if="result.primary_contact_phone_number" dir="ltr">
                            <em class="icon ni ni-call"></em>
                            <span>+966</span>
                            <span v-text="result.primary_contact_phone_number"></span>
                        </span>
                    </span>
                </button>
                <div v-if="!loading && searched && !results.length && !failed" class="previous-delivery-address-search__message">
                    {{ __('contenttypes::strings.shippments.previous_delivery_address_empty') }}
                </div>
                <div v-if="failed" class="previous-delivery-address-search__message text-danger">
                    {{ __('contenttypes::strings.shippments.previous_delivery_address_failed') }}
                </div>
            </div>
        </div>
    </div>
</script>

<script>
    Vue.component('previous-delivery-address-search', {
        template: '#previous-delivery-address-search-template',
        props: {
            cityId: {
                default: null,
            },
        },
        data: function() {
            return {
                query: '',
                results: [],
                opened: false,
                loading: false,
                searched: false,
                failed: false,
                timer: null,
                request: null,
                lastCityId: null,
                lastQuery: null,
            };
        },
        watch: {
            cityId: function() {
                this.reset();
            },
        },
        beforeDestroy: function() {
            this.cancelPending();
        },
        methods: {
            cancelPending: function() {
                if (this.timer) {
                    window.clearTimeout(this.timer);
                    this.timer = null;
                }
                if (this.request && this.request.readyState !== 4) {
                    this.request.abort();
                }
                this.request = null;
            },
            reset: function() {
                this.cancelPending();
                this.query = '';
                this.results = [];
                this.opened = false;
                this.loading = false;
                this.searched = false;
                this.failed = false;
                this.lastCityId = null;
                this.lastQuery = null;
            },
            open: function() {
                if (!this.cityId) {
                    return;
                }
                this.opened = true;
                if (this.lastCityId !== String(this.cityId) || this.lastQuery !== this.query) {
                    this.search();
                }
            },
            closeLater: function() {
                window.setTimeout(() => {
                    this.opened = false;
                }, 150);
            },
            queueSearch: function() {
                this.opened = true;
                if (this.timer) {
                    window.clearTimeout(this.timer);
                }
                this.timer = window.setTimeout(() => this.search(), 300);
            },
            search: function() {
                if (!this.cityId) {
                    this.reset();
                    return;
                }

                this.cancelPending();
                const cityId = String(this.cityId);
                const query = this.query;
                this.loading = true;
                this.failed = false;
                this.opened = true;

                this.request = $.ajax({
                    url: @json(route('ShippmentsController@previousDeliveryAddresses')),
                    method: 'GET',
                    data: {
                        city_id: cityId,
                        search: query,
                    },
                }).done((response) => {
                    if (String(this.cityId) !== cityId || this.query !== query) {
                        return;
                    }
                    this.results = Array.isArray(response.results) ? response.results : [];
                    this.lastCityId = cityId;
                    this.lastQuery = query;
                    this.searched = true;
                }).fail((xhr, status) => {
                    if (status !== 'abort' && String(this.cityId) === cityId) {
                        this.results = [];
                        this.failed = true;
                        this.searched = true;
                    }
                }).always(() => {
                    if (String(this.cityId) === cityId && this.query === query) {
                        this.loading = false;
                    }
                });
            },
            select: function(result) {
                this.query = result.full_address || result.primary_contact_company_name || '';
                this.opened = false;
                this.$emit('selected', result);
            },
        },
    });

    window.fillPreviousDeliveryAddress = function(vm, address, selected) {
        [
            'primary_contact_company_name',
            'primary_contact_name',
            'primary_contact_email',
            'primary_contact_phone_number',
            'full_address',
            'public_notes',
            'private_notes',
        ].forEach(function(field) {
            address[field] = selected[field] || null;
        });

        const lat = Number(selected.lat);
        const lng = Number(selected.lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        address.round_trip = selected.round_trip === 'YES' ? 'YES' : 'NO';
        address.lat = lat;
        address.lng = lng;
        address.latlng = `${address.lat},${address.lng}`;
        address.destination_city_token = null;
        address.city_validation_status = 'pending';

        const placeOnMap = function(attempt) {
            if (!address.map || typeof address.map.placeMarker !== 'function') {
                return;
            }
            const mapState = typeof address.map.getState === 'function' ? address.map.getState() : null;
            if (mapState && mapState.map) {
                address.map.placeMarker(new google.maps.LatLng(address.lat, address.lng));
                return;
            }
            if (attempt < 10) {
                window.setTimeout(() => placeOnMap(attempt + 1), 100);
            }
        };

        placeOnMap(0);
        vm.verifyAddressDestinationCity(address);
    };
</script>
