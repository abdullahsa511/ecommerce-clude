document.addEventListener("DOMContentLoaded", async function () {
    // ############ comment button function ############

    const commentModule = await import('/js/vue/comment.js');

    let modelId = '';
    let modelUuid = '';
    let modelRef = '';
    let modelType = '';
    const commentCommentButtons = document.querySelectorAll('.add-comment-button');
    commentCommentButtons.forEach(function (btn) {
        btn.addEventListener('click', async function (event) {
          try {
              event.preventDefault();
              modelId = btn.getAttribute('data-model-id');
              modelUuid = btn.getAttribute('data-model-uuid');
              modelRef = btn.getAttribute('data-model-ref');
              modelType = btn.getAttribute('data-model-type');
              const payload = { model_id: modelId, model_uuid: modelUuid, model_ref: modelRef, model_type: modelType };
              const commentApp = commentModule.default;
              let appContainer = document.getElementById('comment-app');
              if (appContainer) {
                  await commentApp.loadComments(appContainer, payload);
              } else {
                  console.warn('Customer comment app container not found.');
              }
                  
          } catch (error) {
              console.error('Error loading customer comment app:', error);
          }
      });
    });

    // ############ end comment button function ############
});