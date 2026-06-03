const cursorWrap = document.querySelector(".cursor-follow");
const cursorInner = document.querySelector(".cursor-inner");
const cursorOuter = document.querySelector(".cursor-outer");

let mouseX = 0, mouseY = 0;
let ringX = 0, ringY = 0;

// Jina kam number, utna jayada delay aur smooth (0.05 - 0.1 best hai)
const lag = 0.06;

document.addEventListener("mousemove", (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;

    // Wrapper follows mouse immediately
    cursorWrap.style.transform = `translate(${mouseX}px, ${mouseY}px)`;

    // Inner dot stays centered
    cursorInner.style.transform = `translate(-50%, -50%)`;
});

function animateRing() {
    // Lerp formula for smooth delay
    ringX += (mouseX - ringX) * lag;
    ringY += (mouseY - ringY) * lag;

    // Math Logic Fix:
    // Parent (cursorWrap) mouseX par hai.
    // Hame Outer Ring ko ringX par dikhana hai.
    // Isliye hum offset nikalte hain: (ringX - mouseX)

    cursorOuter.style.transform = `translate(${ringX - mouseX}px, ${ringY - mouseY}px) translate(-50%, -50%)`;

    requestAnimationFrame(animateRing);
}
animateRing();