/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import {
	PanelBody,
	RadioControl,
	Flex,
	FlexItem,
	TextControl,
} from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */

import { useState, useEffect } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

export default function Edit({ attributes, setAttributes }) {
	const { posttype, numPosts } = attributes;

	const [posts, setPosts] = useState([]);

	useEffect(() => {
		const validNumPosts = isNaN(numPosts) ? 4 : numPosts;
		apiFetch({
			path: `/wp/v2/${posttype || "posts"}?per_page=${validNumPosts}&_embed`,
		}).then((posts) => {
			setAttributes({ posts });
			setPosts(posts);
		});
	}, [posttype, numPosts]);

	function abbreviateText(text, length) {
		if (!text) return "";
		const cleanText = text.replace(/<[^>]*>/g, "");

		if (cleanText.length <= length) {
			return cleanText;
		} else {
			const excerpt = cleanText.substr(0, length - 3).trim() + "...";
			return excerpt;
		}
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Settings")}>
					<Flex direction="column">
						<FlexItem>
							<RadioControl
								label="Archive Post Type:"
								selected={posttype || "posts"}
								options={[
									{ label: "Blog", value: "posts" },
									{ label: "Other", value: "other" },
								]}
								onChange={(value) => setAttributes({ posttype: value })}
							/>
						</FlexItem>
						<FlexItem>
							<TextControl
								label={__("Posts (per page)")}
								value={numPosts || ""}
								onChange={(value) => setAttributes({ numPosts: value })}
							/>
						</FlexItem>
					</Flex>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				{posts.length === 0 ? (
					<p
						style={{ textAlign: "center", width: "100%", gridColumn: "span 2" }}
					>
						{__("Loading...")}
					</p>
				) : (
					posts.map((post, index) => (
						<article key={post.id} className={`blogpost`}>
							{post._embedded["wp:featuredmedia"] && (
								<div className="image-container">
									<img
										src={
											post._embedded["wp:featuredmedia"][0].media_details.sizes
												.large.source_url
										}
										alt={post._embedded["wp:featuredmedia"][0].alt_text}
									/>
								</div>
							)}
							<div className="text-container">
								<p>{abbreviateText(post.title.rendered, 37)}</p>
							</div>
						</article>
					))
				)}
			</div>
		</>
	);
}
