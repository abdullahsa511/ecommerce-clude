import ManagePinboard from "./ManagePinboard.js";
import CommunicationModal from "../../pinboard/CommunicationModal.js";
import BookingCalendarModal from "../../pinboard/BookingCalendarModal.js";
import BookingTimeModal from "../../pinboard/BookingTimeModal.js";
import authService from "../../../services/authService.js";
import BookingCallRequestModal from "../../pinboard/BookingCallRequestModal.js";
import BookingConfirmationModal from "../../pinboard/BookingConfirmation.js";
import ProjectSubmissionModal from "../../pinboard/ProjectSubmissionModal.js";

export default {
	name: "AccountPinboardList",
	components: {
		ManagePinboard,
		CommunicationModal,
		BookingCalendarModal,
		BookingTimeModal,
		BookingCallRequestModal,
		BookingConfirmationModal,
		ProjectSubmissionModal,
	},
	data() {
		return {
			showEmbeddedPinboard: false,
			managePinboardLoading: false,
			managePinboardError: null,
			showProjectModal: false,
			showBookingModal: false,
			showBookingTimeModal: false,
			showBookingCallRequestModal: false,
			showBookingConfirmationModal: false,
			bookingSelectedDate: "",
			tourType: "physicalTour",
			discussCustomer: null,
			discussPinboardUuid: null,
			showProjectSubmissionModal: false,
			projectSubmissionType: 'email',
		};
	},

	computed: {
		projectLists() {
			const list = this.$store.getters.projectLists;
			return Array.isArray(list) ? list : [];
		},
		pinboard() {
			return this.$store.getters.pinboard;
		},
		loggedInUser() {
			return this.$store.getters.loggedInUser;
		},
		discussPinboardId() {
			const p = this.pinboard;
			return p?.pinboard_id || p?.pinboard_temp_id;
		},
		discussPinboardTitle() {
			const p = this.pinboard;
			return p?.job_title || p?.pinboard_name || "";
		},
		discussPinboardItemCount() {
			const current = Number(this.pinboard?.item_count);
			if (Number.isFinite(current)) return current;
			const matched = (this.projectLists || []).find(
				(row) => String(row?.pinboard_id) === String(this.discussPinboardId),
			);
			const count = Number(matched?.item_count);
			return Number.isFinite(count) ? count : 0;
		},
		customerForDiscuss() {
			return this.discussCustomer || {};
		},
		discussNearestShowroom() {
			const n = this.$store.getters.nearestShowroom;
			if (n && typeof n === "object" && !Array.isArray(n)) return n;
			return {};
		},
		projectMenuItems() {
			return this.$store.getters.projectItems;
		},
		items: {
			get() {
				console.log("items get component :- ", this.$store.getters.pinboardItems);
				return this.$store.getters.pinboardItems || [];
			},
			set(value) {
				console.log('items set component :- ', value);
				this.$store.dispatch('reorderPinboardItems', value);
			}
		},
		showrooms() {
			return this.$store.getters.showrooms || [];
		},
	},

	created() {
		this.$store.dispatch("getProjectLists");
	},

	methods: {
		showStatus(pinboard){
			let status = pinboard.pinboard_status_name??'Draft';
			if(status === 'Converted To Lead') status = 'In Discussion';
			return status;
		},
		onManagePinboardOffcanvasHidden() {
			this.showEmbeddedPinboard = false;
			this.managePinboardError = null;
		},

		getKey(item, index) {
			return `${item.model_type}-${item.model_id}-${index}`;
		},

		/**
		 * CSS modifier for the status pill from `pinboard_status_id` / `pinboard_status_name`
		 * (order_status table). Keeps label and styling aligned with the API.
		 */
		pinboardStatusPillModifier(pinboard) {
			const id = Number(pinboard?.pinboard_status_id);
			const byId = {
				0: "th-pinboard-status-pill--quote-requested",
				1: "th-pinboard-status-pill--quote-requested",
				2: "th-pinboard-status-pill--draft",
				3: "th-pinboard-status-pill--order",
				4: "th-pinboard-status-pill--order",
				5: "th-pinboard-status-pill--draft",
				6: "th-pinboard-status-pill--draft",
				7: "th-pinboard-status-pill--quote-requested",
				8: "th-pinboard-status-pill--order",
				9: "th-pinboard-status-pill--order",
				10: "th-pinboard-status-pill--order",

			};
			if (Number.isFinite(id) && Object.prototype.hasOwnProperty.call(byId, id)) {
				return byId[id];
			}
			const name = String(pinboard?.pinboard_status_name ?? "")
				.trim()
				.toLowerCase();
			if (!name) {
				return "th-pinboard-status-pill--quote-requested";
			}
			if (
				name === "processed" ||
				name.includes("complet") ||
				name.includes("submitted")
			) {
				return "th-pinboard-status-pill--order";
			}
			if (name.includes("pending") || name.includes("require")) {
				return "th-pinboard-status-pill--quote-requested";
			}
			return "th-pinboard-status-pill--draft";
		},

		normalizeTourType(type) {
			const value = String(type || "").trim();
			if (value === "virtualMeeting") return "virtualMeeting";
			return "physicalTour";
		},
		/**
		 * Bootstrap Modal can leave `body.modal-open` / overflow styles out of sync when a
		 * modal instance is disposed via v-if without a normal hide() (e.g. stacking flows).
		 */
		resetDocumentModalScrollLock() {
			if (typeof document === "undefined") return;
			const body = document.body;
			body.classList.remove("modal-open");
			body.style.removeProperty("overflow");
			body.style.removeProperty("padding-right");
			document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
		},
		closeDiscussModals() {
			this.showProjectModal = false;
			this.showBookingModal = false;
			this.showBookingTimeModal = false;
			this.showProjectSubmissionModal = false;
			this.bookingSelectedDate = "";
			this.tourType = "physicalTour";
			this.$store.commit("SET_NEAREST_SHOWROOM", {});
			this.$store.commit("SET_BOOKED_DATA", []);
			this.$nextTick(() => this.resetDocumentModalScrollLock());
		},
		async handleShowroomVisitBooking(type = "physicalTour") {
			this.showBookingModal = true;
			this.showBookingTimeModal = false;
			this.tourType = this.normalizeTourType(type);
			try {
				await this.$store.dispatch("getNearestShowroom");
			} catch (e) {
				console.error("Failed to load nearest showroom", e);
			}
		},
		async openBookingCallRequestModal() {
			this.showBookingModal = false;
			this.showBookingCallRequestModal = true;
		},
		closeBookingCallRequestModal() {
			this.showBookingCallRequestModal = false;
			this.showBookingModal = false;
		},
		openBookingConfirmationModal() {
			this.showBookingCallRequestModal = false;
			this.showBookingModal = false;
			this.showBookingConfirmationModal = true;
		},
		closeBookingConfirmationModal() {
			this.showBookingConfirmationModal = false;
			this.showBookingModal = false;
			this.showBookingCallRequestModal = false;
		},
		openBookingTimeModal(selectedDate) {
			this.bookingSelectedDate = selectedDate || "";
			this.showBookingTimeModal = true;
		},
		closeBookingTimeModal() {
			this.showBookingTimeModal = false;
			this.showBookingModal = false;
		},
		closeBookingCalendarModal() {
			this.showBookingModal = false;
			this.showBookingTimeModal = false;
			this.bookingSelectedDate = "";
			this.tourType = "physicalTour";
		},
		updateTourType(tourType) {
			this.tourType = this.normalizeTourType(tourType);
		},
		async openDiscussProjectFlow(pinboardId, pinboardUuid) {
			if (!pinboardId) return;

			this.managePinboardLoading = true;
			this.managePinboardError = null;

			const result = await this.$store.dispatch(
				"getProjectItemsByPinboardId",
				pinboardId,
				pinboardUuid,
			);

			this.managePinboardLoading = false;

			if (!result || result.success === false || result.error) {
				this.managePinboardError =
					(result && result.error) ||
					"Could not load this pinboard. Please try again.";
				return;
			}

			try {
				const auth = await authService.getUserAuthentication();
				const c = auth?.customer || {};
				this.discussCustomer = {
					...c,
					is_verified:
						c.is_verified == null ? true : Boolean(c.is_verified),
				};
			} catch (e) {
				console.error("openDiscussProjectFlow: auth", e);
				this.discussCustomer = { is_verified: true };
			}

			this.showBookingModal = false;
			this.showBookingTimeModal = false;
			this.bookingSelectedDate = "";
			this.tourType = "physicalTour";
			if (typeof window.setPinboardModalWidth === "function") {
				window.setPinboardModalWidth();
			}
			this.showProjectModal = true;
			this.discussPinboardUuid = pinboardUuid;
		},
		async openManagePinboardOffcanvas(pinboardId) {
			if (!pinboardId) return;

			this.managePinboardLoading = true;
			this.managePinboardError = null;
			this.showEmbeddedPinboard = false;

			const result = await this.$store.dispatch(
				"getProjectItemsByPinboardId",
				pinboardId,
			);

			// if (
			//   result &&
			//   result.success &&
			//   !result.error &&
			//   this.$store.state.loggedInUser
			// ) {
			//   await pinboardStore.dispatch("getProjectByPinboardId", pinboardId);
			// }

			this.managePinboardLoading = false;

			if (!result || result.success === false || result.error) {
				this.managePinboardError =
					(result && result.error) ||
					"Could not load this pinboard. Please try again.";

				const el = document.getElementById("manage_pinboard_offcanvasRight");
				if (el && window.bootstrap?.Offcanvas) {
					window.bootstrap.Offcanvas.getOrCreateInstance(el).show();
				}
				return;
			}

			this.showEmbeddedPinboard = true;
			await this.$nextTick();

			const el = document.getElementById("manage_pinboard_offcanvasRight");
			if (!el || !window.bootstrap?.Offcanvas) return;

			window.bootstrap.Offcanvas.getOrCreateInstance(el).show();
		},
		async onVisibilityClick(pinboardId, isVisible) {
			await this.$store.dispatch("updatePinboardVisibility", { pinboardId, isVisible });
		},

		async handleProjectSubmissionPopUp(type = 'email') {
			this.showProjectSubmissionModal = true;
			this.showBookingModal = false;
			this.projectSubmissionType = type;
			// try{
			// 	await this.$store.dispatch("getProjectSubmission", { type });
			// } catch (e) {
			// 	console.error("Failed to load project submission", e);
			// }
		},
		closeProjectSubmissionModal() {
			this.showProjectSubmissionModal = false;
		},

		
		async submitProjectSubmission(data) {
			// console.log("submitProjectSubmission component :- ", data);
			try{
				await this.$store.dispatch("submitProjectSubmission", data);
			} catch (e) {
				console.error("Failed to submit project submission", e);
			}
		},
	},

	template: /* html */ `
		<div class="section-body">
			<div class="row">
				<div class="col-12">
				<div class="th-dash-pinboard-devider border-bottom-gray"></div>
				</div>

				<div class="col-12 th-table-content">
					<div class="th-pinboard-table-scroll">
						<table class="th-pinboard-project-table" data-v-pinboard-table>
							<colgroup>
								<col class="th-pinboard-col-project" />
								<col class="th-pinboard-col-saved" />
								<col class="th-pinboard-col-updated" />
								<!-- <col class="th-pinboard-col-testColumn" /> -->
								<col class="th-pinboard-col-status" />
								<col class="th-pinboard-col-actions" />
							</colgroup>
							<thead>
								<tr>
								<th scope="col" class="th-pinboard-col-project" data-v-pinboard-header="name">Project Name</th>
								<th scope="col" class="th-pinboard-col-saved text-center" data-v-pinboard-header="customer">Items</th>
								<th scope="col" class="th-pinboard-col-updated" data-v-pinboard-header="email">Last Updated</th>
								<!-- <th scope="col" class="th-pinboard-col-testColumn text-center" data-v-pinboard-testColumn="testColumn">Test Column</th> -->
								<th scope="col" class="th-pinboard-col-status text-center" data-v-pinboard-header="status">Status</th>
								<th scope="col" class="th-pinboard-col-actions text-center" data-v-pinboard-header="actions">Actions</th>
								</tr>
							</thead>
							<tbody data-v-pinboard-rows>
								<tr
									data-v-pinboard-row
									v-for="pinboard in projectLists"
									:key="pinboard.pinboard_id"
									:class="{ 'th-pinboard-row--disabled': pinboard.is_visible == 0 }"
									:aria-disabled="!pinboard.is_visible == 0 ? 'true' : 'false'"
								>
									<td class="th-pinboard-col-project font-wight-700" data-label="Project name" data-v-pinboard-name >
										<a class="" @click.prevent="openManagePinboardOffcanvas(pinboard.pinboard_id)">{{ pinboard.pinboard_name }}</a>
									</td>
									<td class="th-pinboard-col-saved text-center" data-label="Saved items" data-v-pinboard-customer-name>
										{{ pinboard.item_count }}
									</td>
									<td class="th-pinboard-col-updated" data-label="Last updated" data-v-pinboard-customer-email>
										{{ pinboard.updated_at ? pinboard.updated_at : pinboard.created_at }}
									</td>
									<td class="th-pinboard-col-status text-center" data-label="Status">
										<!--<span data-v-pinboard-status class="th-pinboard-status-pill th-pinboard-status-pill--draft">Draft</span>-->
										<span data-v-pinboard-status class="th-pinboard-status-pill" :class="pinboardStatusPillModifier(pinboard)">
											{{  showStatus(pinboard) }}
										</span>
									</td>
									<!-- <td class="th-pinboard-col-test-column text-center" data-label="Test Column"> <span>test Column </span> </td> -->
									<td class="th-pinboard-col-actions th-pinboard-actions-cell text-center" data-label="Actions" data-v-pinboard-link >
										<div class="th-pinboard-actions-inner">
											<div class="th-pinboard-actions-icons" role="toolbar" aria-label="Project quick actions">
												<button
													type="button"
													class="th-pinboard-actions-icon-btn th-pinboard-actions-icon-btn--toggle"
													@click.prevent="onVisibilityClick(pinboard.pinboard_id, pinboard.is_visible == 1 ? 0 : 1)"
												>
													<i
														class="fa-solid"
														:class="pinboard.is_visible == 1 ? 'fa-eye' : 'fa-eye-slash'"
														aria-hidden="true"
													></i>
												</button>
												<span class="th-pinboard-actions-divider" aria-hidden="true"></span>
												<a
													href="#"
													role="button"
													class="th-pinboard-actions-icon-btn"
													title="Open project"
													data-v-pinboard-view
													@click.prevent="openManagePinboardOffcanvas(pinboard.pinboard_id)"
												>
													<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
												</a>
												<a
												    v-if="Number(pinboard.item_count) > 0"
													href="#"
													role="button"
													class="th-pinboard-actions-cta text-capitalize"
													@click.prevent="openDiscussProjectFlow(pinboard.pinboard_id, pinboard.uuid)"
												>
													Discuss
												</a>
											</div>
											
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- ============================== Start manage pinboard offcanvas ============================== -->
			<ManagePinboard
			:items="items"
			:pinboard="pinboard"
			:loading="managePinboardLoading"
			:error="managePinboardError"
			@hidden="onManagePinboardOffcanvasHidden"
			/>
			<!-- ============================== end manage pinboard offcanvas ============================== -->

			<communication-modal
				v-if="showProjectModal && customerForDiscuss?.is_verified && !showBookingModal && !showBookingCallRequestModal"
				:suppress-for-overlay="showProjectSubmissionModal"
				:showProjectSuccessMessage="false"
				:customer="customerForDiscuss"
				:pinboard-title="discussPinboardTitle"
				:pinboard-id="discussPinboardId"
				:pinboard-uuid="discussPinboardUuid"
				:logged-in-user="loggedInUser"
				:show-booking-modal="showBookingModal"
				@close="closeDiscussModals"
				@open-booking-call-request="openBookingCallRequestModal"
				@book-showroom="handleShowroomVisitBooking"
				@project-submission-popup="handleProjectSubmissionPopUp"
			></communication-modal>

			<booking-calendar-modal
				v-if="showProjectModal && customerForDiscuss?.is_verified && showBookingModal && !showBookingTimeModal"
				:pinboard-title="discussPinboardTitle"
				:pinboard-id="discussPinboardId"
				:nearest-showroom="discussNearestShowroom"
				:tour-type="tourType"
				@close-booking="closeBookingCalendarModal"
				@open-time-slots="openBookingTimeModal"
				@update-tour-type="updateTourType"
				:showrooms="showrooms"
			></booking-calendar-modal>

			<booking-time-modal
				v-if="showProjectModal && customerForDiscuss?.is_verified && showBookingModal && showBookingTimeModal"
				:pinboard-title="discussPinboardTitle"
				:pinboard-id="discussPinboardId"
				:pinboard-uuid="discussPinboardUuid"
				:selected-date="bookingSelectedDate"
				:tour-type="tourType"
				:customer="customerForDiscuss"
				:logged-in-user="loggedInUser"
				:nearest-showroom="discussNearestShowroom"
				@close-time="closeBookingTimeModal"
				@back-to-calendar="closeBookingTimeModal"
				@booking-success="closeDiscussModals"
				page="manage_pinboard"
			></booking-time-modal>

			<booking-call-request-modal
				v-if="showProjectModal && customerForDiscuss?.is_verified && showBookingCallRequestModal"
				:pinboard-title="discussPinboardTitle"
				:pinboard-id="discussPinboardId"
				:pinboard-uuid="discussPinboardUuid"
				:logged-in-user="loggedInUser"
				:nearest-showroom="discussNearestShowroom"
				@close="closeBookingCallRequestModal"
				@submit-success="openBookingConfirmationModal"
				page="manage_pinboard"
			></booking-call-request-modal>

			<booking-confirmation-modal
				v-if="showProjectModal && customerForDiscuss?.is_verified && showBookingConfirmationModal"
				:pinboard-title="discussPinboardTitle"
				:pinboard-id="discussPinboardId"
				:pinboard-uuid="discussPinboardUuid"
				:logged-in-user="loggedInUser"
				:nearest-showroom="discussNearestShowroom"
				@close="closeBookingConfirmationModal"
				page="manage_pinboard"
			></booking-confirmation-modal>

			<project-submission-modal
                v-if="showProjectModal && customerForDiscuss?.is_verified && showProjectSubmissionModal"
                :customer="customerForDiscuss"
                :pinboard-title="discussPinboardTitle"
                :pinboard-item-count="discussPinboardItemCount"
                :pinboard-id="discussPinboardId"
                :pinboard-uuid="discussPinboardUuid"
                :logged-in-user="loggedInUser"
                :show-booking-modal="showBookingModal"
                :submission-type="projectSubmissionType"
                @close-project-submission="closeProjectSubmissionModal"
                @submit-success="openBookingConfirmationModal"
				page="manage_pinboard"
            ></project-submission-modal>
		</div>
  `,
};
