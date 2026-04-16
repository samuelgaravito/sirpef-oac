import jsPDF from "jspdf";

const generatePuntoDeCuentaPDF = (form: any, outputType: 'inline' | 'bloburl' = 'inline'): string | void => {
  const data = form || {};
  const doc = new jsPDF({ unit: 'mm', format: 'letter' });
  const pageWidth = doc.internal.pageSize.getWidth();
  const pageHeight = doc.internal.pageSize.getHeight();
  const margin = 7;
  let yPos = 2;

  // --- Constantes de Diseño ---
  const FONT_MAIN = 'helvetica';
  const COLOR_VINO_HEADER_BAR = '#c00000';
  const COLOR_VINO_TITLE_BG = '#C00000';
  const COLOR_TEXT_ON_VINO = '#FFFFFF';
  const COLOR_BORDER_SECTION = '#000000';
  const COLOR_TEXT_DARK = '#000000';
  const BORDER_LINE_WIDTH = 0.2;
  const boxSize = 4;
  
  // --- ALTURA FIJA PARA EL PIE DE PÁGINA (FIRMAS) ---
  const FOOTER_HEIGHT = 40;
  const PAGE_BREAK_LIMIT = pageHeight - margin - FOOTER_HEIGHT;

  const drawFooter = (docInstance: jsPDF) => {
    const firmaStartY = pageHeight - FOOTER_HEIGHT;
    const firmaBlockWidth = (pageWidth - (margin * 2) - 10) / 2;
    const firmaTextMaxWidth = firmaBlockWidth - 4;
    const firmaLineaY = firmaStartY + 3;
    docInstance.setDrawColor(COLOR_TEXT_DARK);
    docInstance.setLineWidth(0.3);
    docInstance.line(margin + 15, firmaLineaY, margin + firmaBlockWidth - 15, firmaLineaY);
    docInstance.line(margin + firmaBlockWidth + 10 + 15, firmaLineaY, margin + firmaBlockWidth + 10 + firmaBlockWidth - 15, firmaLineaY);
    const yFirmaTextStart = firmaLineaY + 3;
    let yFirmaIzquierda = yFirmaTextStart;
    docInstance.setFontSize(9);
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.text((data.presentado_por || '').toUpperCase(), margin + firmaBlockWidth / 2, yFirmaIzquierda, { maxWidth: firmaTextMaxWidth, align: 'center' });
    yFirmaIzquierda += 4;
    docInstance.setFont(FONT_MAIN, 'normal');
    docInstance.text(data.cargo_por || '', margin + firmaBlockWidth / 2, yFirmaIzquierda, { maxWidth: firmaTextMaxWidth, align: 'center' });
    yFirmaIzquierda += 4;
    const resIzquierdaLines = docInstance.splitTextToSize(data.resolucion_1 || '', firmaTextMaxWidth);
    docInstance.text(resIzquierdaLines, margin + firmaBlockWidth / 2, yFirmaIzquierda, { align: 'center' });
    let yFirmaDerecha = yFirmaTextStart;
    const xFirmaDerecha = margin + firmaBlockWidth + 10 + firmaBlockWidth / 2;
    docInstance.setFontSize(9);
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.text((data.presentado_a || '').toUpperCase(), xFirmaDerecha, yFirmaDerecha, { maxWidth: firmaTextMaxWidth, align: 'center' });
    yFirmaDerecha += 4;
    docInstance.setFont(FONT_MAIN, 'normal');
    docInstance.text(data.cargo_a || '', xFirmaDerecha, yFirmaDerecha, { maxWidth: firmaTextMaxWidth, align: 'center' });
    yFirmaDerecha += 4;
    const res1DerechaLines = docInstance.splitTextToSize(data.resolucion_2 || '', firmaTextMaxWidth);
    docInstance.text(res1DerechaLines, xFirmaDerecha, yFirmaDerecha, { align: 'center' });
  };

  const drawHeader = (docInstance: jsPDF) => {
    let localY = 2;
    const cintilloImgPath = '/cintillo.png';
    const cintilloHeight = 13;
    const cintilloWidth = pageWidth - (margin * 2);
    try {
      docInstance.addImage(cintilloImgPath, 'PNG', margin, localY, cintilloWidth, cintilloHeight);
      localY += cintilloHeight;
    } catch (e) { console.error("Error al cargar la imagen del cintillo:", e); localY += 10; }
    
    const headerBar1Y = localY;
    const headerBar1Height = 7;
    const headerBar2Height = 5;
    const puntoNroBoxWidth = 48;
    const ministerioBoxWidth = pageWidth - (margin * 2) - puntoNroBoxWidth;
    const xRightColumn = margin + ministerioBoxWidth;
    const yBottomBar = headerBar1Y + headerBar1Height;
    docInstance.setFillColor(COLOR_VINO_HEADER_BAR);
    docInstance.rect(margin, headerBar1Y, pageWidth - (margin * 2), headerBar1Height, 'F');
    docInstance.rect(margin, yBottomBar, ministerioBoxWidth, headerBar2Height, 'F');
    docInstance.setFillColor('#FFFFFF');
    docInstance.rect(xRightColumn, yBottomBar, puntoNroBoxWidth, headerBar2Height, 'F');
    docInstance.setDrawColor(COLOR_BORDER_SECTION);
    docInstance.setLineWidth(0.1);
    docInstance.rect(xRightColumn, yBottomBar, puntoNroBoxWidth, headerBar2Height);
    docInstance.line(xRightColumn, headerBar1Y, xRightColumn, yBottomBar + headerBar2Height);
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.setTextColor(COLOR_TEXT_ON_VINO);
    docInstance.setFontSize(11);
    docInstance.text('MINISTERIO DEL PODER POPULAR DE ECONOMÍA Y FINANZAS', margin + ministerioBoxWidth / 2, headerBar1Y + 4.5, { align: 'center' });
    const puntoNroTexto = `PUNTO N° ${data.numero_punto || ''}`;
    docInstance.text(puntoNroTexto, margin + ministerioBoxWidth + (puntoNroBoxWidth / 2), headerBar1Y + (headerBar1Height / 2) + 1.5, { align: 'center' });
    docInstance.text('PUNTO DE CUENTA', margin + ministerioBoxWidth / 2, headerBar1Y + headerBar1Height + 3.5, { align: 'center' });
    
    localY += headerBar1Height + headerBar2Height;
    docInstance.setTextColor(COLOR_TEXT_DARK);
    
    const blockHeight = 18;
    const presentadoLabelWidth = 30;
    const infoBoxWidth = 48;
    const middleBoxWidth = pageWidth - (margin * 2) - presentadoLabelWidth - infoBoxWidth;
    const xPresentado = margin;
    const xMiddle = margin + presentadoLabelWidth;
    docInstance.rect(xPresentado, localY, pageWidth - (margin * 2), blockHeight);
    docInstance.line(xMiddle, localY, xMiddle, localY + blockHeight);
    docInstance.line(xMiddle + middleBoxWidth, localY, xMiddle + middleBoxWidth, localY + blockHeight);
    docInstance.line(xMiddle + middleBoxWidth, localY + blockHeight / 2, xMiddle + middleBoxWidth + infoBoxWidth, localY + blockHeight / 2);
    docInstance.setFontSize(11);
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.text('PRESENTADO', xPresentado + presentadoLabelWidth / 2, localY + blockHeight / 2 + 1.5, { align: 'center' });
    let yMiddleText = localY + 4.5;
    docInstance.text('A:', xMiddle + 2, yMiddleText);
    docInstance.text((data.presentado_a || '').toUpperCase(), xMiddle + 2 + docInstance.getTextWidth('A: ') + 1, yMiddleText, {maxWidth: middleBoxWidth - 15});
    yMiddleText += 4;
    docInstance.setFont(FONT_MAIN, 'normal');
    docInstance.text(data.cargo_a || '', xMiddle + 2, yMiddleText, {maxWidth: middleBoxWidth - 5});
    const lineY = localY + blockHeight / 2;
    docInstance.line(xMiddle, lineY, xMiddle + middleBoxWidth, lineY);
    yMiddleText = lineY + 4.5;
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.text('Por:', xMiddle + 2, yMiddleText);
    docInstance.text((data.presentado_por || '').toUpperCase(), xMiddle + 2 + docInstance.getTextWidth('Por: ') + 1, yMiddleText, {maxWidth: middleBoxWidth - 15});
    yMiddleText += 4;
    docInstance.setFont(FONT_MAIN, 'normal');
    docInstance.text(data.cargo_por || '', xMiddle + 2, yMiddleText, {maxWidth: middleBoxWidth - 5});
    docInstance.setFont(FONT_MAIN, 'bold');
    const xInfo = xMiddle + middleBoxWidth;
    const infoBoxCenterX = xInfo + infoBoxWidth / 2;
    const yTopHalfCenter = localY + (blockHeight / 4);
    const yBottomHalfCenter = localY + (blockHeight * 0.75);
    docInstance.text('Nº Páginas:', infoBoxCenterX, yTopHalfCenter - 0.5, { align: 'center' });
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.text('Fecha:', infoBoxCenterX, yBottomHalfCenter - 0.5, { align: 'center' });
    docInstance.setFont(FONT_MAIN, 'normal');
    
    // Formateo de la fecha para el PDF
    let fechaParaPdf = 'dd/mm/aaaa';
    if (data.fecha && data.fecha.includes('-')) {
        const [year, month, day] = data.fecha.split('-');
        fechaParaPdf = `${day}/${month}/${year}`;
    }
    docInstance.text(fechaParaPdf, infoBoxCenterX, yBottomHalfCenter + 3, { align: 'center' });
    
    localY += blockHeight;
    localY = drawSectionWithStyledTitleAndBox('ASUNTO:', data.asunto || '', localY, docInstance, false);
    return localY;
  };

  const checkPageBreak = (currentY: number, requiredHeight: number): number => {
    if (currentY + requiredHeight > PAGE_BREAK_LIMIT) {
      drawFooter(doc);
      doc.addPage();
      return drawHeader(doc);
    }
    return currentY;
  };

  const drawSectionWithStyledTitleAndBox = (title: string, content: string, startY: number, docInstance: jsPDF = doc, checkBreak = true): number => {
    const titleBarHeight = 6;
    const contentPadding = 2;
    const sectionContentMaxWidth = pageWidth - (margin * 2) - (contentPadding * 2);
    const sectionTotalWidth = pageWidth - (margin * 2);

    const words: { text: string; isBold: boolean }[] = [];
    (content || '').split(/(\*\*.*?\*\*)/g).filter(p => p.length > 0).forEach(part => {
        const isBold = part.startsWith('**') && part.endsWith('**');
        const cleanPart = isBold ? part.slice(2, -2) : part;
        cleanPart.split(/\s+/).filter(w => w.length > 0).forEach(word => words.push({ text: word, isBold: isBold }));
    });

    docInstance.setFontSize(11);
    const lineHeightFactor = 1.15;
    const lineHeight = (docInstance.getFontSize() / docInstance.internal.scaleFactor) * lineHeightFactor;
    const spaceWidth = docInstance.getTextWidth(' ');
    const lines: { words: { text: string; isBold: boolean }[]; width: number }[] = [];
    let currentLine: { text: string; isBold: boolean }[] = [];
    let currentLineWidth = 0;

    words.forEach(wordObj => {
        docInstance.setFont(FONT_MAIN, wordObj.isBold ? 'bold' : 'normal');
        const wordWidth = docInstance.getTextWidth(wordObj.text);
        if (currentLineWidth + wordWidth + (currentLine.length > 0 ? spaceWidth : 0) > sectionContentMaxWidth) {
            lines.push({ words: currentLine, width: currentLineWidth });
            currentLine = [wordObj];
            currentLineWidth = wordWidth;
        } else {
            currentLine.push(wordObj);
            currentLineWidth += wordWidth + (currentLine.length > 0 ? spaceWidth : 0);
        }
    });
    if (currentLine.length > 0) lines.push({ words: currentLine, width: currentLineWidth });

    const contentHeight = lines.length * lineHeight;
    const totalSectionHeight = titleBarHeight + contentHeight + (contentPadding * 2);

    let currentY = checkBreak ? checkPageBreak(startY, totalSectionHeight) : startY;
    
    docInstance.setFillColor(COLOR_VINO_TITLE_BG);
    docInstance.rect(margin, currentY, sectionTotalWidth, titleBarHeight, 'F');
    docInstance.setFontSize(11);
    docInstance.setFont(FONT_MAIN, 'bold');
    docInstance.setTextColor(COLOR_TEXT_ON_VINO);
    docInstance.text(title.toUpperCase(), margin + contentPadding, currentY + titleBarHeight - 2);
    
    const textStartY = currentY + titleBarHeight + contentPadding + 3;
    lines.forEach((line, index) => {
        const isLastLine = index === lines.length - 1;
        const numSpaces = line.words.length - 1;
        let effectiveSpaceWidth = spaceWidth;
        if (!isLastLine && numSpaces > 0 && line.width < sectionContentMaxWidth) {
            effectiveSpaceWidth = (sectionContentMaxWidth - (line.width - numSpaces * spaceWidth)) / numSpaces;
        }
        let currentX = margin + contentPadding;
        const lineY = textStartY + (index * lineHeight);
        line.words.forEach((wordObj, wordIndex) => {
            docInstance.setFont(FONT_MAIN, wordObj.isBold ? 'bold' : 'normal');
            docInstance.setTextColor(COLOR_TEXT_DARK);
            docInstance.text(wordObj.text, currentX, lineY);
            currentX += docInstance.getTextWidth(wordObj.text) + (wordIndex < line.words.length - 1 ? effectiveSpaceWidth : 0);
        });
    });
    
    docInstance.setDrawColor(COLOR_TEXT_DARK);
    docInstance.setLineWidth(BORDER_LINE_WIDTH);
    docInstance.rect(margin, currentY, sectionTotalWidth, totalSectionHeight);
    return currentY + totalSectionHeight;
  };

  yPos = drawHeader(doc);
  
  yPos = drawSectionWithStyledTitleAndBox('EXPOSICIÓN DE MOTIVO:', data.exposicion_motivos || '', yPos, doc);
  yPos = drawSectionWithStyledTitleAndBox('PROPUESTA:', data.propuesta || '', yPos, doc);
  
  const decisionSectionHeight = 28;
  yPos = checkPageBreak(yPos, decisionSectionHeight);
  const decisionSectionStartY = yPos;
  const decisionTitleBarHeight = 6;
  doc.setFillColor(COLOR_VINO_TITLE_BG);
  doc.rect(margin, yPos, pageWidth - (margin * 2), decisionTitleBarHeight, 'F');
  doc.setFontSize(11);
  doc.setFont(FONT_MAIN, 'bold');
  doc.setTextColor(COLOR_TEXT_ON_VINO);
  doc.text('DECISIÓN:', margin + 1.5, yPos + decisionTitleBarHeight - 2);
  const decisionContentY = yPos + decisionTitleBarHeight;
  doc.setTextColor(COLOR_TEXT_DARK);
  const decisionOptions = ['APROBADO', 'NEGADO', 'VISTO', 'DIFERIDO', 'OTRO'];
  let xDecisionStart = margin;
  const decisionOptionWidth = (pageWidth - (margin * 2)) / decisionOptions.length;
  const yLabel = decisionContentY + 4;
  const yBox = yLabel + 3;
  decisionOptions.forEach(opt => {
    const centerX = xDecisionStart + (decisionOptionWidth / 2);
    doc.setFont(FONT_MAIN, 'bold');
    doc.text(opt, centerX, yLabel, { align: 'center' });
    const boxX = centerX - (boxSize / 2);
    doc.setLineWidth(BORDER_LINE_WIDTH);
    doc.rect(boxX, yBox, boxSize, boxSize);
    if (data.decision === opt) doc.text('X', boxX + 1, yBox + boxSize - 0.5);
    xDecisionStart += decisionOptionWidth;
  });
  yPos = yBox + boxSize + 1;
  doc.setDrawColor(COLOR_BORDER_SECTION);
  doc.setLineWidth(BORDER_LINE_WIDTH);
  doc.rect(margin, decisionSectionStartY, pageWidth - (margin * 2), yPos - decisionSectionStartY);
  
  const otrasInstStartY = yPos;
  const contentPadding = 2;
  const lineHeightOtras = 4.2;
  const topPadding = 4;
  doc.setFontSize(11);
  const textWidthOtrasInstrucciones = pageWidth - margin - 48 - contentPadding * 2;
  let otrasInstWrappedLines = doc.splitTextToSize(data.otras_instrucciones || " ", textWidthOtrasInstrucciones);
  const textHeight = otrasInstWrappedLines.length * lineHeightOtras;
  const sectionHeight = Math.max(16, textHeight + topPadding + contentPadding);
  
  const newYPos = checkPageBreak(otrasInstStartY, sectionHeight);
  
  doc.setFont(FONT_MAIN, 'bold');
  doc.setTextColor(COLOR_TEXT_DARK);
  doc.text('Otras instrucciones:', margin + contentPadding, newYPos + topPadding + 1);
  doc.setFont(FONT_MAIN, 'normal');
  doc.text(otrasInstWrappedLines, margin + contentPadding, newYPos + topPadding + 5);
  const anexoContentY = newYPos + topPadding + 1;
  const anexoXStart = pageWidth - margin - 42;
  doc.setFont(FONT_MAIN, 'bold');
  doc.text('Anexos:', anexoXStart, anexoContentY);
  let currentAnexoX = anexoXStart + doc.getTextWidth('Anexos:') + 2;
  doc.text('Si', currentAnexoX, anexoContentY - 1);
  currentAnexoX += doc.getTextWidth('Si') + 1.5;
  doc.rect(currentAnexoX, anexoContentY - 4, boxSize, boxSize);
  if (data.anexos === true) doc.text('X', currentAnexoX + 1, anexoContentY - 0.7);
  currentAnexoX += boxSize + 4;
  doc.text('No', currentAnexoX, anexoContentY - 1);
  currentAnexoX += doc.getTextWidth('No') + 1.5;
  doc.rect(currentAnexoX, anexoContentY - 4, boxSize, boxSize);
  if (data.anexos === false) doc.text('X', currentAnexoX + 1, anexoContentY - 0.7);
  const dividerX = anexoXStart - 2;
  doc.line(dividerX, newYPos, dividerX, newYPos + sectionHeight);
  doc.setDrawColor(COLOR_BORDER_SECTION);
  doc.setLineWidth(BORDER_LINE_WIDTH);
  doc.rect(margin, newYPos, pageWidth - (margin * 2), sectionHeight);

  drawFooter(doc);

  const totalPages = doc.internal.getNumberOfPages();
  for (let i = 1; i <= totalPages; i++) {
    doc.setPage(i);
    const pageInfoString = `${i}/${totalPages}`;
    
    const infoBoxWidth = 48;
    const blockHeightHeader = 18;
    const ministerioBoxWidth = pageWidth - (margin * 2) - 30 - infoBoxWidth;
    const xInfo = margin + 30 + ministerioBoxWidth;
    const yHeaderEnd = 2 + 13 + 7 + 5;
    const yTopHalfCenter = yHeaderEnd + (blockHeightHeader / 4);

    const placeholderX = xInfo + infoBoxWidth / 2;
    const placeholderY = yTopHalfCenter + 3;

    doc.setFillColor('#FFFFFF');
    doc.rect(placeholderX - 10, placeholderY - 2.5, 20, 3.5, 'F');
    doc.setFontSize(11);
    doc.setFont(FONT_MAIN, 'normal');
    doc.setTextColor(COLOR_TEXT_DARK);
    doc.text(pageInfoString, placeholderX, placeholderY, { align: 'center' });
  }

  if (outputType === 'bloburl') {
    return doc.output('bloburl');
  } else {
    const pdfOutput = doc.output('blob');
    const pdfUrl = URL.createObjectURL(pdfOutput);
    window.open(pdfUrl);
  }
};

export default generatePuntoDeCuentaPDF;