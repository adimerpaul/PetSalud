// src/addons/Imprimir.js
import { Printd } from 'printd'

export class Imprimir {
  // -------------------------
  // Helpers
  // -------------------------
  static money (n) {
    const x = Number(n || 0)
    return x.toFixed(2)
  }

  static safe (v) {
    return v === null || v === undefined ? '' : String(v)
  }

  static fmtDate (iso) {
    if (!iso) return ''
    // "2025-12-27T01:53:36.000000Z" -> "2025-12-27 01:53"
    return String(iso).replace('T', ' ').slice(0, 16)
  }

  static makeStyles (widthPx = 300) {
    return `
      <style>
        * { box-sizing: border-box; }
        body {
          font-family: Arial, sans-serif;
          margin: 0;
          padding: 0;
          color: #111;
        }

        .ticket {
          width: ${widthPx}px;
          padding: 10px 10px 12px 10px;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: 700; }
        .muted { color: #555; }
        .tiny { font-size: 10px; }
        .small { font-size: 11px; }
        .base { font-size: 12px; }

        .title {
          font-size: 14px;
          font-weight: 800;
          letter-spacing: .2px;
          margin-bottom: 2px;
        }

        .subtitle {
          font-size: 11px;
          margin-top: 2px;
          line-height: 1.15;
        }

        .hr {
          border: 0;
          border-top: 1px dashed #000;
          margin: 8px 0;
        }

        .row {
          display: flex;
          justify-content: space-between;
          gap: 8px;
          line-height: 1.2;
        }

        table {
          width: 100%;
          border-collapse: collapse;
        }

        th, td {
          padding: 3px 0;
          vertical-align: top;
        }

        thead th {
          font-size: 11px;
          border-bottom: 1px solid #000;
          padding-bottom: 4px;
        }

        tbody td {
          font-size: 11px;
        }

        tfoot td {
          border-top: 1px solid #000;
          padding-top: 6px;
          font-size: 12px;
          font-weight: 700;
        }

        .col-qty { width: 34px; }
        .col-name { width: auto; }
        .col-sub { width: 70px; text-align: right; }

        .item-name {
          font-weight: 700;
          line-height: 1.1;
        }

        .item-meta {
          font-size: 10px;
          color: #444;
          line-height: 1.1;
          margin-top: 1px;
        }

        .footer {
          margin-top: 10px;
          font-size: 10px;
          text-align: center;
          color: #333;
          line-height: 1.2;
        }

        @media print {
          @page { margin: 0; }
        }
      </style>
    `
  }

  // -------------------------
  // NOTA DE VENTA (SALE JSON)
  // -------------------------
  static nota (sale) {
    const s = sale?.sale ? sale.sale : sale // por si mandas {sale:{...}} o el objeto directo
    if (!s) return

    const vet = s.veterinaria || {}
    const mascota = s.mascota || {}
    const user = s.user || {}
    const details = Array.isArray(s.details) ? s.details : []

    const titulo = (String(s.tipo || 'Venta').toLowerCase() === 'gasto')
      ? 'NOTA DE GASTO'
      : 'NOTA DE VENTA'

    const total = details.reduce((acc, d) => acc + Number(d.subtotal || 0), 0)
    const totalFinal = Number(s.total || total || 0)

    const headerHtml = `
      <div class="center">
        <div class="title">${this.safe(vet.nombre) || 'VETERINARIA'}</div>
        <div class="subtitle muted">
          ${this.safe(vet.direccion)}<br>
          Tel: ${this.safe(vet.telefono)}${vet.email ? `<br>${this.safe(vet.email)}` : ''}
        </div>
      </div>
      <hr class="hr">
      <div class="row small">
        <div><span class="bold">Fecha:</span> ${this.fmtDate(s.fecha || s.fechaCreacion)}</div>
        <div><span class="bold">#</span> ${this.safe(s.id)}</div>
      </div>
      <div class="row small">
        <div><span class="bold">Pago:</span> ${this.safe(s.pago)}</div>
        <div><span class="bold">Usuario:</span> ${this.safe(user.username || user.name)}</div>
      </div>
      <div class="small" style="margin-top:6px;">
        <span class="bold">Mascota:</span> ${this.safe(mascota.nombre)}<br>
        <span class="bold">Propietario:</span> ${this.safe(mascota.propietario_nombre)}
      </div>
      ${s.comentarioDoctor ? `<div class="small" style="margin-top:6px;"><span class="bold">Comentario:</span> ${this.safe(s.comentarioDoctor)}</div>` : ''}
      <hr class="hr">
      <div class="center bold base">${titulo}</div>
    `

    const rows = details.map(d => {
      const qty = Number(d.cantidad || 0)
      const precio = Number(d.precio || 0)
      const sub = Number(d.subtotal || (qty * precio) || 0)

      return `
        <tr>
          <td class="col-qty">${qty}</td>
          <td class="col-name">
            <div class="item-name">${this.safe(d.productoName)}</div>
            <div class="item-meta">P/U: ${this.money(precio)} Bs</div>
          </td>
          <td class="col-sub">${this.money(sub)} Bs</td>
        </tr>
      `
    }).join('')

    const html = `
      <div class="ticket">
        ${headerHtml}
        <hr class="hr">

        <table>
          <thead>
            <tr>
              <th class="col-qty">Cant</th>
              <th class="col-name">Detalle</th>
              <th class="col-sub">Subt</th>
            </tr>
          </thead>
          <tbody>
            ${rows || `<tr><td colspan="3" class="center small muted">Sin detalles</td></tr>`}
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" class="right">TOTAL</td>
              <td class="right">${this.money(totalFinal)} Bs</td>
            </tr>
          </tfoot>
        </table>

        <div class="footer">
          Gracias por su compra 🤍<br>
          ${this.safe(vet.descripcion)}
        </div>
      </div>
    `

    const d = new Printd()
    const container = document.getElementById('myElement')
    if (container) container.innerHTML = html

    // Imprime el HTML con estilos
    d.print(container || this._tmpEl(html), [this.makeStyles(300)])
  }

  // Crea un elemento temporal si no existe myElement
  static _tmpEl (html) {
    const el = document.createElement('div')
    el.innerHTML = html
    return el
  }
}
