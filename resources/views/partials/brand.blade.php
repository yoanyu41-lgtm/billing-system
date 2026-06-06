{{-- ============================================================
     CityTech Billing — Brand Design Tokens (Navy + Emerald)
     Single source of truth for colors, radius, shadows.
     Premium, flat, NO gradients. See DESIGN-SYSTEM.md.
     Included in every layout <head>.
============================================================ --}}
<style>
    :root {
        /* ── Brand ── */
        --brand:            #1E3A5F;   /* deep professional navy (primary)   */
        --brand-hover:      #162D47;   /* navy hover / pressed               */
        --brand-active:     #112138;   /* navy darkest                       */
        --on-brand:         #FFFFFF;

        --secondary:        #2563EB;   /* support blue                       */
        --secondary-hover:  #1D4ED8;

        --accent:           #059669;   /* emerald — "paid / success"         */
        --accent-hover:     #047857;
        --accent-soft:      #34D399;   /* emerald highlight on dark surfaces */

        /* ── Sidebar (dark navy surface) ── */
        --sidebar:          #0F1E33;
        --sidebar-raised:   #18293F;
        --sidebar-text:     #94A3B8;
        --sidebar-text-hi:  #E2E8F0;
        --sidebar-hover:    rgba(255,255,255,0.06);

        /* ── Semantic status ── */
        --success:          #059669;
        --warning:          #D97706;
        --danger:           #DC2626;
        --info:             #2563EB;

        /* ── Surfaces & text ── */
        --bg:               #F1F5F9;
        --surface:          #FFFFFF;
        --surface-muted:    #F8FAFC;
        --text:             #0F172A;
        --text-muted:       #64748B;
        --text-subtle:      #94A3B8;
        --border:           #E2E8F0;
        --border-strong:    #CBD5E1;
        --ring:             #1E3A5F;

        /* ── Radius ── */
        --radius-sm: 10px;
        --radius:    14px;
        --radius-lg: 18px;
        --radius-pill: 999px;

        /* ── Premium layered shadows (soft, never harsh) ── */
        --shadow-sm: 0 1px 2px rgba(15,23,42,.06);
        --shadow:    0 1px 3px rgba(15,23,42,.08), 0 1px 2px rgba(15,23,42,.04);
        --shadow-md: 0 4px 14px rgba(15,23,42,.08);
        --shadow-lg: 0 12px 32px -8px rgba(15,23,42,.16);
        --shadow-brand: 0 8px 20px -6px rgba(30,58,95,.45);
        --shadow-accent: 0 8px 20px -6px rgba(5,150,105,.40);
    }
</style>
