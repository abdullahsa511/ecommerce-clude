<template>
    <div v-if="showLiveCamera"
        class="position-fixed top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3"
        style="z-index: 1070; background: rgba(0,0,0,0.85);" @click.self="cancelLiveCamera">
        <div class="w-100" style="max-width: 520px;" @click.stop>
            <video ref="liveCameraVideo" class="w-100 rounded bg-dark"
                style="max-height: min(55vh, 420px); object-fit: cover;" playsinline muted autoplay></video>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="button" class="btn btn-sm btn-outline-light" @click="cancelLiveCamera">Cancel</button>
                <button type="button" class="btn btn-sm th-btn-primary text-capitalize"
                    @click="snapLiveCameraPhoto">Take photo</button>
            </div>
        </div>
    </div>
</template>