<style>
    /* Forum Prototype Styles */
    .forum-container {
        max-width: 100%;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .post {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        background: white;
        position: relative;
        transition: all 0.3s;
    }

    .post-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-right: 15px;
        flex-shrink: 0;
        text-transform: uppercase;
        overflow: hidden;
        border: 3px solid transparent;
    }

    .avatar.teacher {
        background: #1565c0;
        border-color: #4caf50;
    }

    /* Blue for Teacher */
    .avatar.student {
        background: #10b981;
        border-color: #00a8e8;
    }

    /* Green for Student */

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .post-info {
        flex-grow: 1;
    }

    .author-name {
        font-weight: 600;
        color: #2c5282;
        margin-right: 10px;
        font-size: 15px;
    }

    .badge-role {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 5px;
    }

    .badge-siswa {
        background: #00a8e8;
        color: white;
    }

    .badge-guru {
        background: #4caf50;
        color: white;
    }

    .post-date {
        color: #666;
        font-size: 13px;
        margin-top: 3px;
    }

    .post-content {
        color: #333;
        font-size: 14px;
        margin-bottom: 15px;
        padding-left: 63px;
        white-space: pre-wrap;
    }

    .post-question {
        font-weight: 500;
        margin-top: 8px;
    }

    .reply-btn {
        position: absolute;
        top: 20px;
        right: 20px;

        background: #4caf50;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: background 0.3s;
        text-decoration: none;
    }

    .reply-btn:hover {
        background: #45a049;
        color: white;
    }

    .reply-btn::before {
        content: "↩";
        font-size: 16px;
    }

    /* Nested Replies */
    .reply-node {
        margin-left: 40px;
        border-left: 3px solid #e0e0e0;
        padding-left: 20px;
    }

    .scroll-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: white;
        border: 2px solid #e0e0e0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.3s;
        z-index: 999;
    }

    .scroll-btn:hover {
        background: #f5f5f5;
        transform: translateY(-2px);
    }

    /* Forms */
    .reply-form {
        display: none;
        margin-top: 15px;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-left: 63px;
        /* Align with content */
    }

    .reply-form.active {
        display: block;
    }

    .reply-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
        min-height: 80px;
        margin-bottom: 10px;
    }

    .action-icons {
        position: absolute;
        top: 60px;
        right: 20px;
        display: flex;
        gap: 10px;
    }

    .action-icon {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        opacity: 0.6;
        transition: opacity 0.3s;
        padding: 0;
    }

    .action-icon:hover {
        opacity: 1;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .reply-node {
            margin-left: 20px;
            padding-left: 15px;
        }

        .post-content,
        .reply-form {
            padding-left: 0;
        }

        .reply-btn {
            position: static;
            margin-top: 10px;
            width: fit-content;
        }

        .action-icons {
            position: static;
            margin-top: 10px;
            justify-content: flex-end;
        }
    }

    .hidden {
        display: none !important;
    }

    .highlight {
        animation: highlight 2s ease-out;
    }

    @keyframes highlight {
        0% {
            background-color: #e3f2fd;
        }

        100% {
            background-color: white;
        }
    }

    /* Attachment & Dropzone Styles */
    .attachment-toggle-btn {
        background: none;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 13px;
        cursor: pointer;
        color: #666;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
        margin-bottom: 10px;
    }

    .attachment-toggle-btn:hover,
    .attachment-toggle-btn.active {
        background: #f0f0f0;
        color: #333;
        border-color: #ccc;
    }

    .attachment-area {
        display: none;
        margin-bottom: 15px;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropzone-wrapper {
        border: 2px dashed #ccc;
        border-radius: 6px;
        padding: 20px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        position: relative;
        transition: all 0.3s;
    }

    .dropzone-wrapper:hover {
        background: #f0f0f0;
        border-color: #999;
    }

    .dropzone-wrapper.dragover {
        background: #e3f2fd;
        border-color: #2196f3;
    }

    .dropzone-desc {
        color: #666;
        font-size: 14px;
    }

    .dropzone-desc i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
        color: #999;
    }

    .dropzone-file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-preview-name {
        margin-top: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #2196f3;
        display: none;
    }
</style>
