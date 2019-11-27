# USAGE
# python recognize_digits.py

# import the necessary packages
import sys
from imutils.perspective import four_point_transform
from imutils import contours
import imutils
import cv2

# define the dictionary of digit segments so we can identify
# each digit on the thermostat
DIGITS_LOOKUP = {
	(1, 1, 1, 0, 1, 1, 1): 0,
	(0, 0, 1, 0, 0, 1, 0): 1,
	(0, 1, 0, 0, 1, 0, 0): 1,
	(1, 0, 1, 1, 1, 0, 1): 2,
	(1, 0, 1, 1, 0, 1, 1): 3,
	(0, 1, 1, 1, 0, 1, 0): 4,
	(1, 1, 0, 1, 0, 1, 1): 5,
	(1, 1, 0, 1, 1, 1, 1): 6,
	(1, 0, 1, 0, 0, 1, 0): 7,
	(1, 1, 1, 1, 1, 1, 1): 8,
	(1, 1, 1, 1, 0, 1, 1): 9
}

# load the example image
gambar = sys.argv[1]
image = cv2.imread(gambar)

# pre-process the image by resizing it, converting it to
# graycale, blurring it, and computing an edge map
image = imutils.resize(image, height=100)
gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
#cv2.imshow("Gray", gray)
blurred = cv2.GaussianBlur(gray, (5, 5), 0)
#cv2.imshow("blurred", blurred)
#edged = cv2.Canny(blurred, 50, 200, 255)
#cv2.imshow("Edged", edged)

treshold = cv2.threshold(blurred, 220, 255, cv2.THRESH_BINARY_INV)[1]
#cv2.imshow("treshold awal", treshold)
kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (1, 5))
treshold = cv2.morphologyEx(treshold, cv2.MORPH_OPEN, kernel)
#cv2.imshow("treshold", treshold)
# find contours in the edge map, then sort them by their
# size in descending order
"""
cnts = cv2.findContours(edged.copy(), cv2.RETR_EXTERNAL,
	cv2.CHAIN_APPROX_SIMPLE)
cnts = cnts[0] if imutils.is_cv2() else cnts[1]
cnts = sorted(cnts, key=cv2.contourArea, reverse=True)
displayCnt = None

# loop over the contours
for c in cnts:
	# approximate the contour
	peri = cv2.arcLength(c, True)
	approx = cv2.approxPolyDP(c, 0.02 * peri, True)

	# if the contour has four vertices, then we have found
	# the thermostat display
	if len(approx) == 4:
		displayCnt = approx
		break

# extract the thermostat display, apply a perspective transform
# to it
warped = four_point_transform(gray, displayCnt.reshape(4, 2))
output = four_point_transform(image, displayCnt.reshape(4, 2))

# threshold the warped image, then apply a series of morphological
# operations to cleanup the thresholded image
thresh = cv2.threshold(warped, 0, 255,
	cv2.THRESH_BINARY_INV | cv2.THRESH_OTSU)[1]
kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (1, 5))
thresh = cv2.morphologyEx(thresh, cv2.MORPH_OPEN, kernel)
"""
# find contours in the thresholded image, then initialize the
# digit contours lists

cnts = cv2.findContours(treshold.copy(), cv2.RETR_EXTERNAL,
	cv2.CHAIN_APPROX_SIMPLE)
cnts = cnts[0] if imutils.is_cv2() else cnts[1]
digitCnts = []

# loop over the digit area candidates
for c in cnts:
	# compute the bounding box of the contour
	(x, y, w, h) = cv2.boundingRect(c)	
	# if the contour is sufficiently large, it must be a digit
	#if w >= 15 and (h >= 55 and h <= 70):
	if w >= 10:
		digitCnts.append(c)

# sort the contours from left-to-right, then initialize the
# actual digits themselves


digitCnts = contours.sort_contours(digitCnts,
	method="left-to-right")[0]
digits = []

# loop over each of the digits
index = 0
for c in digitCnts:
	# extract the digit ROI
	(x, y, w, h) = cv2.boundingRect(c)
	#print "index ke "+str(index) + " lebarnya "+str(w)
	#print "index ke "+str(index) + " tingginya "+str(h)
	if(w < 45):
		w = 45
	roi = treshold[y:y + h, x:x + w]
	indexcv = "r "+str(index)
	index = index + 1
	cv2.imshow(indexcv, roi)
	# compute the width and height of each of the 7 segments
	# we are going to examine
	(roiH, roiW) = roi.shape
	(dW, dH) = (int(roiW * 0.25), int(roiH * 0.15))
	dHC = int(roiH * 0.05)

	# define the set of 7 segments
	segments = [
		((0, 0), (w, dH)),	# top
		((0, 0), (dW, h // 2)),	# top-left
		((w - dW, 0), (w, h // 2)),	# top-right
		((0, (h // 2) - dHC) , (w, (h // 2) + dHC)), # center
		((0, h // 2), (dW, h)),	# bottom-left
		((w - dW, h // 2), (w, h)),	# bottom-right
		((0, h - dH), (w, h))	# bottom
	]
	on = [0] * len(segments)

	# loop over the segments
	for (i, ((xA, yA), (xB, yB))) in enumerate(segments):
		# extract the segment ROI, count the total number of
		# thresholded pixels in the segment, and then compute
		# the area of the segment
		segROI = roi[yA:yB, xA:xB]
		total = cv2.countNonZero(segROI)
		area = (xB - xA) * (yB - yA)

		# if the total number of non-zero pixels is greater than
		# 50% of the area, mark the segment as "on"
		if area > 0:
			if total / float(area) > 0.42:
				on[i]= 1

	# lookup the digit and draw it on the image	
	if tuple(on) in DIGITS_LOOKUP:
		digit = DIGITS_LOOKUP[tuple(on)]	
		digits.append(str(digit))
		#print(digit)
	#print(on)	
	#print("---------------------------")
#print(digits)
#	cv2.rectangle(output, (x, y), (x + w, y + h), (0, 255, 0), 1)
#	cv2.putText(output, str(digit), (x - 10, y - 10),
#		cv2.FONT_HERSHEY_SIMPLEX, 0.65, (0, 255, 0), 2)

# display the digits
#print(u"{}{}.{} \u00b0C".format(*digits))
#cv2.imshow("Input", image)
#cv2.imshow("Output", output)
print(''.join(digits))
#print cv2.waitKey(0)