document.addEventListener("click", function (e) {
  const btn = e.target.closest(".vote-btn");
  if (!btn) return;
  e.preventDefault();

  const postId = btn.dataset.postId;
  const value = parseInt(btn.dataset.value, 10);
  if (!postId) return;

  btn.disabled = true;
  fetch("/vote", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      target_type: "post",
      target_id: postId,
      value: value,
    }),
  })
    .then((res) => {
      if (!res.ok) throw res;
      return res.json();
    })
    .then((data) => {
      if (data && typeof data.likes !== "undefined") {
        const postEl = document.querySelector(`[data-post="${postId}"]`);
        if (!postEl) return;
        const likeEl = postEl.querySelector(".like-count");
        const dislikeEl = postEl.querySelector(".dislike-count");
        if (likeEl) likeEl.textContent = data.likes;
        if (dislikeEl) dislikeEl.textContent = data.dislikes;

        // update 'Your vote' text
        let voteTextEl = postEl.querySelector(".your-vote-text");
        if (!voteTextEl) {
          voteTextEl = document.createElement("span");
          voteTextEl.className = "your-vote-text text-xs text-gray-500";
          postEl.querySelector(".flex-1").appendChild(voteTextEl);
        }
        if (data.user_vote === 1) voteTextEl.textContent = "Your vote: Liked";
        else if (data.user_vote === -1)
          voteTextEl.textContent = "Your vote: Disliked";
        else voteTextEl.textContent = "";
      }
    })
    .catch(async (err) => {
      try {
        const json = await err.json();
        console.error("Vote error", json);
        alert(json.error || "Vote failed");
      } catch (e) {
        console.error(e);
        alert("Vote failed");
      }
    })
    .finally(() => {
      btn.disabled = false;
    });
});
