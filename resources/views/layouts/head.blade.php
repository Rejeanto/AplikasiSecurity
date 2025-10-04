<head>
  <title>Security Head Office</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="Mantis is made using Bootstrap 5 design framework." />
  <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template" />
  <meta name="author" content="CodedThemes" />

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" />

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />

  {{-- Custom CSS tambahan --}}
  <style>
    :root {
      --accent: #f28b6b;
      --muted: #e9ecef;
      --text-muted: #6c757d;
    }
    .stepper {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1.2rem;
      margin: 1.25rem 0;
    }
    .step {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 110px;
      z-index: 1;
      cursor: pointer;
    }
    .step::before {
      content: "";
      position: absolute;
      top: 18px;
      left: -55%;
      width: 110%;
      height: 3px;
      background: var(--muted);
      z-index: 0;
    }
    .step:first-child::before {
      display: none;
    }
    .step-number {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      background: white;
      border: 2px solid #dee2e6;
      color: var(--text-muted);
      z-index: 2;
    }
    .step-label {
      margin-top: 0.45rem;
      font-size: 0.85rem;
      color: var(--text-muted);
      text-align: center;
    }
    .step.active .step-number,
    .step.completed .step-number {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }
    .step.completed + .step::before {
      background: var(--accent);
    }
    .step.active .step-label {
      color: #000;
      font-weight: 600;
    }
    .btn-teal {
      background-color: #14b8a6;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn-teal:hover {
      background-color: #0d9488;
    }
  </style>
</head>
