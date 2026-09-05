<div>
    @if ($logo)
        <div style="
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.2);
            box-shadow: 0 2px 8px rgba(0,0,0,0.35);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        ">
            <img
                src="data:image/png;base64,{{ $logo }}"
                alt="Logo da empresa"
                style="width: 100%; height: 100%; object-fit: cover;"
            />
        </div>
    @endif
</div>
