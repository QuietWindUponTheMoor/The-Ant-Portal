<button class="btn-main" id="create-post-button">Create Post</button>

<!--<div class="create-post-ask-type-modal">
    <div class="modal-top">
        <p class="modal-title">What type of post would you like to create?</p>
    </div>
    <div class="modal-bottom">
        <div class="modal-section">
        </div>
        <div class="modal-section">
            <button class="btn-secondary" id="cancel-create-post-modal">Cancel</button>
        </div>
    </div>
</div>-->

<div class="modal-main" id="create-post-ask-type-modal">
    <div class="modal-section">
        <div class="modal-sub-section">
            <p class="modal-title">What type of post would you like to create?</p>
        </div>
    </div>
    <div class="modal-section" id="selections">
        <a class="btn-action option" href="/create_post/">General</a>
        <a class="btn-action option" href="/create_post/">Question</a>
        <a class="btn-action option" href="/create_post/">Sighting</a>
        <a class="btn-action option" href="/create_nup_flight/">Nuptial Flight</a>
    </div>
</div>

<script type="text/javascript">
$("#create-post-button").on("click", () => {
    $("#create-post-ask-type-modal").css("display", "flex").hide().fadeIn();
});

$("#cancel-create-post-modal").on("click", () => {
    $("#create-post-ask-type-modal").css("display", "none");
});
</script>

